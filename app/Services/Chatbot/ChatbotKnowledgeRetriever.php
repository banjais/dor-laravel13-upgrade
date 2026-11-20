<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotKnowledgeChunk;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ChatbotKnowledgeRetriever
{
    /**
     * Search only indexed, active, public knowledge for the requested office.
     *
     * @return array{question:string,office_id:int,matches:array}
     */
    public function search(string $question, int $officeId, int $limit = 5): array
    {
        $question = trim($question);

        if ($question === '') {
            throw new InvalidArgumentException('Search question is required.');
        }

        if ($officeId < 1) {
            throw new InvalidArgumentException('A valid office is required.');
        }

        $limit = max(1, min($limit, 10));
        $terms = $this->terms($question);

        if ($terms === []) {
            return [
                'question' => $question,
                'office_id' => $officeId,
                'matches' => [],
            ];
        }

        $query = ChatbotKnowledgeChunk::query()
            ->with(['document'])
            ->where(function ($scope) use ($officeId) {
                $scope->where('office_id', $officeId)
                    ->orWhereNull('office_id');
            })
            ->where('status', 'active')
            ->whereHas('document', function ($documents) use ($officeId) {
                $documents
                    ->where(function ($scope) use ($officeId) {
                        $scope->where('office_id', $officeId)
                            ->orWhereNull('office_id');
                    })
                    ->where('parser_status', 'indexed')
                    ->whereHas('source', function ($sources) use ($officeId) {
                        $sources
                            ->where('is_enabled', true)
                            ->where('is_public', true)
                            ->where(function ($scope) use ($officeId) {
                                $scope->where('office_id', $officeId)
                                    ->orWhereNull('office_id');
                            });
                    });
            })
            ->where(function ($search) use ($terms) {
                foreach ($terms as $term) {
                    $pattern = '%' . addcslashes($term, '\\%_') . '%';
                    $search->orWhere('chunk_text', 'LIKE', $pattern);
                }
            });

        // Bound database work even when a common word matches many records.
        $candidates = $query
            ->orderByDesc('id')
            ->limit(300)
            ->get();

        $ranked = $candidates
            ->map(function (ChatbotKnowledgeChunk $chunk) use ($terms) {
                $text = mb_strtolower($chunk->chunk_text, 'UTF-8');
                $score = 0;

                foreach ($terms as $term) {
                    if (mb_strpos($text, $term, 0, 'UTF-8') !== false) {
                        $score++;
                    }
                }

                return [
                    'chunk' => $chunk,
                    'score' => $score,
                ];
            })
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sort(function (array $a, array $b) {
                return ($b['score'] <=> $a['score'])
                    ?: ($b['chunk']->id <=> $a['chunk']->id);
            })
            ->take($limit)
            ->values();

        return [
            'question' => $question,
            'office_id' => $officeId,
            'matches' => $ranked->map(function (array $item) {
                $chunk = $item['chunk'];
                $document = $chunk->document;

                return [
                    'document_id' => (int) $document->id,
                    'chunk_id' => (int) $chunk->id,
                    'chunk_number' => (int) $chunk->chunk_number,
                    'title' => $document->title,
                    'source_url' => $document->source_url,
                    'content' => $chunk->chunk_text,
                    'score' => (int) $item['score'],
                ];
            })->all(),
        ];
    }

    /**
     * Keep Nepali and English Unicode words; ignore one-character fragments.
     */
    private function terms(string $question): array
    {
        $normalized = mb_strtolower($question, 'UTF-8');
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $normalized) ?: [];

        $terms = [];

        foreach ($parts as $part) {
            if (mb_strlen($part, 'UTF-8') >= 2) {
                $terms[] = $part;
            }
        }

        return array_values(array_unique($terms));
    }
}
