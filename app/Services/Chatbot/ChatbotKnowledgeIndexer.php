<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotKnowledgeDocument;
use App\Models\ChatbotKnowledgeSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class ChatbotKnowledgeIndexer
{
    private const CHUNK_SIZE = 1200;
    private const CHUNK_OVERLAP = 150;

    public function __construct(
        private readonly ChatbotDocumentExtractor $extractor
    ) {
    }

    /**
     * Extract and index a local document.
     *
     * Required: title, storage_disk, storage_path.
     * Optional: source_id, office_id, language_id, document_type,
     * source_key, source_url, mime_type, source_updated_at, metadata.
     */
    public function indexDocument(array $data): array
    {
        foreach (['title', 'storage_disk', 'storage_path'] as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // A source is the authority for its office and must be enabled.
        // Never allow caller-supplied office_id to move source content.
        $source = null;

        if (!empty($data['source_id'])) {
            $source = ChatbotKnowledgeSource::findOrFail($data['source_id']);

            if (!$source->is_enabled) {
                throw new InvalidArgumentException('Knowledge source is disabled.');
            }

            if (array_key_exists('office_id', $data)
                && $data['office_id'] !== null
                && (int) $data['office_id'] !== (int) $source->office_id) {
                throw new InvalidArgumentException(
                    'Document office_id does not match its knowledge source.'
                );
            }
        }

        $disk = (string) $data['storage_disk'];
        $path = (string) $data['storage_path'];

        if (!in_array($disk, ['local', 'public'], true)) {
            throw new InvalidArgumentException('Unapproved storage disk.');
        }

        $extraction = $this->extractor->extract($disk, $path);

        // Never replace an existing successful index after extraction failure.
        if (($extraction['status'] ?? null) !== 'extracted'
            || trim((string) ($extraction['text'] ?? '')) === '') {
            return [
                'status' => 'failed',
                'error' => $extraction['error'] ?? 'No extractable text found.',
                'document' => null,
                'chunks' => 0,
            ];
        }

        $absolutePath = Storage::disk($disk)->path($path);

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return [
                'status' => 'failed',
                'error' => 'Document is not available for indexing.',
                'document' => null,
                'chunks' => 0,
            ];
        }

        $checksum = hash_file('sha256', $absolutePath);

        if ($checksum === false) {
            throw new RuntimeException('Unable to calculate document checksum.');
        }

        $source = null;

        if (!empty($data['source_id'])) {
            $source = ChatbotKnowledgeSource::findOrFail($data['source_id']);
        }

        $officeId = $data['office_id'] ?? $source?->office_id;
        $languageId = $data['language_id'] ?? null;
        $sourceId = $source?->id;

        $documentQuery = ChatbotKnowledgeDocument::query();

        if ($sourceId !== null) {
            $documentQuery->where('source_id', $sourceId);
        } else {
            $documentQuery->whereNull('source_id');
        }

        if (!empty($data['source_key'])) {
            $documentQuery->where('source_key', $data['source_key']);
        } else {
            $documentQuery
                ->where('storage_disk', $disk)
                ->where('storage_path', $path);
        }

        $existing = $documentQuery->first();

        // Identical content with existing chunks needs no re-indexing.
        if ($existing
            && $existing->checksum === $checksum
            && $existing->parser_status === 'indexed'
            && $existing->chunks()->exists()) {
            return [
                'status' => 'unchanged',
                'document' => $existing,
                'chunks' => $existing->chunks()->count(),
                'checksum' => $checksum,
            ];
        }

        $chunks = $this->splitText((string) $extraction['text']);

        if ($chunks === []) {
            return [
                'status' => 'failed',
                'error' => 'Document did not produce usable text chunks.',
                'document' => null,
                'chunks' => 0,
            ];
        }

        // All document changes and chunk replacement are atomic.
        $document = DB::transaction(function () use (
            $existing,
            $data,
            $source,
            $sourceId,
            $officeId,
            $languageId,
            $disk,
            $path,
            $checksum,
            $extraction,
            $chunks
        ) {
            $document = $existing
                ? ChatbotKnowledgeDocument::query()
                    ->whereKey($existing->id)
                    ->lockForUpdate()
                    ->firstOrFail()
                : new ChatbotKnowledgeDocument();

            $document->fill([
                'source_id' => $sourceId,
                'office_id' => $officeId,
                'language_id' => $languageId,
                'document_type' => $data['document_type'] ?? 'document',
                'source_key' => $data['source_key'] ?? null,
                'title' => $data['title'],
                'source_url' => $data['source_url'] ?? null,
                'storage_disk' => $disk,
                'storage_path' => $path,
                'mime_type' => $data['mime_type'] ?? null,
                'file_size' => filesize(Storage::disk($disk)->path($path)),
                'checksum' => $checksum,
                'source_updated_at' => $data['source_updated_at'] ?? null,
                'extracted_text' => $extraction['text'],
                'extraction_method' => $extraction['method'] ?? null,
                'parser_status' => 'indexed',
                'indexed_at' => now(),
                'last_error' => null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $document->save();

            // Old chunks are removed only inside the successful transaction.
            $document->chunks()->delete();

            foreach ($chunks as $number => $chunkText) {
                $document->chunks()->create([
                    'source_id' => $sourceId,
                    'office_id' => $officeId,
                    'language_id' => $languageId,
                    'chunk_number' => $number + 1,
                    'chunk_text' => $chunkText,
                    'token_count' => max(
                        1,
                        (int) ceil(mb_strlen($chunkText, 'UTF-8') / 4)
                    ),
                    'chunk_hash' => hash('sha256', $chunkText),
                    'status' => 'active',
                ]);
            }

            if ($source) {
                $source->forceFill([
                    'last_indexed_at' => now(),
                    'content_checksum' => $checksum,
                    'last_error' => null,
                ])->save();
            }

            return $document->fresh();
        });

        return [
            'status' => 'indexed',
            'document' => $document,
            'chunks' => count($chunks),
            'checksum' => $checksum,
            'method' => $extraction['method'] ?? null,
        ];
    }

    /**
     * Split Unicode text without cutting Nepali characters.
     */
    public function splitText(string $text): array
    {
        $text = trim(str_replace("\0", '', $text));

        if ($text === '') {
            return [];
        }

        $result = [];
        $length = mb_strlen($text, 'UTF-8');
        $start = 0;

        while ($start < $length) {
            $remaining = $length - $start;
            $take = min(self::CHUNK_SIZE, $remaining);
            $piece = mb_substr($text, $start, $take, 'UTF-8');

            if ($remaining > self::CHUNK_SIZE) {
                $newline = mb_strrpos($piece, "\n", 0, 'UTF-8');
                $space = mb_strrpos($piece, ' ', 0, 'UTF-8');
                $boundary = max(
                    $newline === false ? -1 : $newline,
                    $space === false ? -1 : $space
                );

                // Avoid producing very small chunks at an early boundary.
                if ($boundary >= (int) (self::CHUNK_SIZE * 0.60)) {
                    $take = $boundary + 1;
                    $piece = mb_substr($text, $start, $take, 'UTF-8');
                }
            }

            $piece = trim($piece);

            if ($piece !== '') {
                $result[] = $piece;
            }

            if ($start + $take >= $length) {
                break;
            }

            $nextStart = $start + $take - self::CHUNK_OVERLAP;

            // Guarantee forward progress.
            $start = max($start + 1, $nextStart);
        }

        return $result;
    }
}
