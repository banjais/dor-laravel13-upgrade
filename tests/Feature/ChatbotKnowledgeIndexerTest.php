<?php

namespace Tests\Feature;

use App\Models\ChatbotKnowledgeDocument;
use App\Models\ChatbotKnowledgeChunk;
use App\Models\ChatbotKnowledgeSource;
use App\Services\Chatbot\ChatbotKnowledgeIndexer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class ChatbotKnowledgeIndexerTest extends TestCase
{
    private ChatbotKnowledgeIndexer $indexer;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('chatbot_knowledge_chunks');
        Schema::dropIfExists('chatbot_knowledge_documents');
        Schema::dropIfExists('chatbot_knowledge_sources');

        Schema::create('chatbot_knowledge_sources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->string('source_type', 40);
            $table->string('name', 200);
            $table->text('source_url')->nullable();
            $table->string('storage_disk', 50)->nullable();
            $table->text('storage_path')->nullable();
            $table->string('external_id', 191)->nullable();
            $table->longText('auth_config')->nullable();
            $table->string('language_code', 20)->default('both');
            $table->string('status', 30)->default('pending');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_public')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_indexed_at')->nullable();
            $table->char('content_checksum', 64)->nullable();
            $table->string('source_version', 100)->nullable();
            $table->text('last_error')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('document_type', 40)->default('webpage');
            $table->string('source_key', 191)->nullable();
            $table->string('title', 500);
            $table->text('source_url')->nullable();
            $table->string('storage_disk', 50)->nullable();
            $table->text('storage_path')->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->char('checksum', 64)->nullable();
            $table->timestamp('source_updated_at')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->string('extraction_method', 40)->nullable();
            $table->string('parser_status', 30)->default('pending');
            $table->timestamp('indexed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->unsignedInteger('chunk_number');
            $table->longText('chunk_text');
            $table->unsignedInteger('token_count')->default(0);
            $table->char('chunk_hash', 64);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(
                ['document_id', 'chunk_number'],
                'cb_chunks_document_number_unique'
            );
        });

        Storage::fake('local');

        $this->indexer = app(ChatbotKnowledgeIndexer::class);
    }

    private function makeDocx(string $path, string $text): void
    {
        Storage::disk('local')->makeDirectory(dirname($path));

        $absolute = Storage::disk('local')->path($path);
        $zip = new ZipArchive();

        if ($zip->open($absolute, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create DOCX fixture.');
        }

        $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $zip->addFromString(
            'word/document.xml',
            '<?xml version="1.0" encoding="UTF-8"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body><w:p><w:r><w:t>' . $escaped . '</w:t></w:r></w:p></w:body>'
            . '</w:document>'
        );

        $zip->close();
    }

    private function input(string $path, string $title = 'DoR Test Document'): array
    {
        return [
            'title' => $title,
            'document_type' => 'document',
            'source_key' => 'dor-test-document',
            'storage_disk' => 'local',
            'storage_path' => $path,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'office_id' => 4,
            'language_id' => 1,
        ];
    }

    public function test_document_is_extracted_and_indexed_into_chunks(): void
    {
        $path = 'chatbot-tests/index.docx';
        $this->makeDocx($path, 'Department of Roads सडक विभाग');

        $result = $this->indexer->indexDocument($this->input($path));

        $this->assertSame('indexed', $result['status']);
        $this->assertSame(1, $result['chunks']);
        $this->assertSame('indexed', $result['document']->parser_status);
        $this->assertSame(4, $result['document']->office_id);
        $this->assertSame(1, $result['document']->language_id);

        $chunk = $result['document']->chunks()->first();

        $this->assertNotNull($chunk);
        $this->assertSame(4, $chunk->office_id);
        $this->assertSame(1, $chunk->language_id);
        $this->assertStringContainsString('सडक विभाग', $chunk->chunk_text);
        $this->assertSame(hash('sha256', $chunk->chunk_text), $chunk->chunk_hash);
    }

    public function test_identical_document_is_not_indexed_twice(): void
    {
        $path = 'chatbot-tests/same.docx';
        $this->makeDocx($path, 'Department of Roads सडक विभाग');

        $first = $this->indexer->indexDocument($this->input($path));
        $second = $this->indexer->indexDocument($this->input($path));

        $this->assertSame('indexed', $first['status']);
        $this->assertSame('unchanged', $second['status']);
        $this->assertSame($first['document']->id, $second['document']->id);
        $this->assertSame(1, ChatbotKnowledgeDocument::count());
        $this->assertSame(1, $second['document']->chunks()->count());
    }

    public function test_updated_document_replaces_old_chunks(): void
    {
        $path = 'chatbot-tests/update.docx';

        $this->makeDocx($path, 'OLD CONTENT सडक विभाग');
        $first = $this->indexer->indexDocument($this->input($path));

        $this->makeDocx($path, 'NEW CONTENT नेपाल सरकार सडक विभाग');
        $second = $this->indexer->indexDocument($this->input($path));

        $this->assertSame('indexed', $second['status']);
        $this->assertSame($first['document']->id, $second['document']->id);
        $this->assertStringContainsString('NEW CONTENT', $second['document']->extracted_text);
        $this->assertStringNotContainsString('OLD CONTENT', $second['document']->extracted_text);
        $this->assertSame(1, $second['document']->chunks()->count());
    }

    public function test_failed_extraction_preserves_previous_successful_index(): void
    {
        $path = 'chatbot-tests/failure.docx';

        $this->makeDocx($path, 'PREVIOUS GOOD CONTENT सडक विभाग');
        $first = $this->indexer->indexDocument($this->input($path));
        $oldChunk = $first['document']->chunks()->first()->chunk_text;

        Storage::disk('local')->put($path, 'not a valid DOCX archive');

        $failed = $this->indexer->indexDocument($this->input($path));

        $this->assertSame('failed', $failed['status']);

        $document = ChatbotKnowledgeDocument::findOrFail($first['document']->id);

        $this->assertSame('indexed', $document->parser_status);
        $this->assertStringContainsString('PREVIOUS GOOD CONTENT', $document->extracted_text);
        $this->assertSame($oldChunk, $document->chunks()->first()->chunk_text);
        $this->assertSame(1, $document->chunks()->count());
    }

    public function test_chunking_preserves_nepali_unicode_and_splits_long_text(): void
    {
        $text = str_repeat('नेपाल सरकार सडक विभाग सार्वजनिक सूचना । ', 100);
        $chunks = $this->indexer->splitText($text);

        $this->assertGreaterThan(1, count($chunks));

        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(1200, mb_strlen($chunk, 'UTF-8'));
            $this->assertStringContainsString('नेपाल', $chunk);
        }
    }

    public function test_source_office_is_authoritative_when_indexing(): void
    {
        $path = 'chatbot-tests/source-office.docx';
        $this->makeDocx($path, 'सडक विभागको आधिकारिक सूचना');

        $source = ChatbotKnowledgeSource::create([
            'office_id' => 9,
            'source_type' => 'document',
            'name' => 'Office 9 source',
            'status' => 'pending',
            'is_enabled' => true,
            'is_public' => true,
        ]);

        $data = $this->input($path);
        unset($data['office_id']);
        $data['source_id'] = $source->id;

        $result = $this->indexer->indexDocument($data);

        $this->assertSame('indexed', $result['status']);
        $this->assertSame(9, (int) $result['document']->office_id);
        $this->assertSame(9, (int) $result['document']->chunks()->first()->office_id);
    }

    public function test_source_office_mismatch_is_rejected(): void
    {
        $path = 'chatbot-tests/source-office-mismatch.docx';
        $this->makeDocx($path, 'सडक विभागको आधिकारिक सूचना');

        $source = ChatbotKnowledgeSource::create([
            'office_id' => 9,
            'source_type' => 'document',
            'name' => 'Office 9 source',
            'status' => 'pending',
            'is_enabled' => true,
            'is_public' => true,
        ]);

        $data = $this->input($path);
        $data['source_id'] = $source->id;
        $data['office_id'] = 4;

        try {
            $this->indexer->indexDocument($data);
            $this->fail('A source office mismatch must be rejected.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(
                'Document office_id does not match its knowledge source.',
                $exception->getMessage()
            );
        }

        $this->assertSame(0, ChatbotKnowledgeDocument::count());
    }

    public function test_disabled_source_cannot_be_indexed(): void
    {
        $path = 'chatbot-tests/disabled-source.docx';
        $this->makeDocx($path, 'सडक विभागको आधिकारिक सूचना');

        $source = ChatbotKnowledgeSource::create([
            'office_id' => 4,
            'source_type' => 'document',
            'name' => 'Disabled source',
            'status' => 'pending',
            'is_enabled' => false,
            'is_public' => true,
        ]);

        $data = $this->input($path);
        $data['source_id'] = $source->id;

        try {
            $this->indexer->indexDocument($data);
            $this->fail('A disabled knowledge source must be rejected.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertSame(
                'Knowledge source is disabled.',
                $exception->getMessage()
            );
        }

        $this->assertSame(0, ChatbotKnowledgeDocument::count());
    }

    public function test_missing_required_fields_are_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->indexer->indexDocument([
            'title' => 'Missing storage information',
        ]);
    }

    private function createRetrievalFixture(
        int $officeId,
        string $content,
        bool $enabled = true,
        bool $public = true,
        string $parserStatus = 'indexed',
        string $chunkStatus = 'active'
    ): void {
        $source = ChatbotKnowledgeSource::create([
            'office_id' => $officeId,
            'source_type' => 'document',
            'name' => 'Retrieval test source',
            'status' => 'pending',
            'is_enabled' => $enabled,
            'is_public' => $public,
        ]);

        $document = ChatbotKnowledgeDocument::create([
            'source_id' => $source->id,
            'office_id' => $officeId,
            'language_id' => 1,
            'document_type' => 'document',
            'title' => 'DoR Retrieval Test',
            'source_url' => 'https://dor.gov.np/test-document',
            'extracted_text' => $content,
            'parser_status' => $parserStatus,
        ]);

        \App\Models\ChatbotKnowledgeChunk::create([
            'document_id' => $document->id,
            'source_id' => $source->id,
            'office_id' => $officeId,
            'language_id' => 1,
            'chunk_number' => 0,
            'chunk_text' => $content,
            'token_count' => 10,
            'chunk_hash' => hash('sha256', $content),
            'status' => $chunkStatus,
        ]);
    }

    public function test_retriever_returns_public_indexed_content_with_source_citation(): void
    {
        $this->createRetrievalFixture(
            4,
            'Department of Roads सडक विभाग सार्वजनिक सूचना'
        );

        $result = app(\App\Services\Chatbot\ChatbotKnowledgeRetriever::class)
            ->search('सडक विभाग', 4);

        $this->assertCount(1, $result['matches']);
        $this->assertSame('DoR Retrieval Test', $result['matches'][0]['title']);
        $this->assertSame(
            'https://dor.gov.np/test-document',
            $result['matches'][0]['source_url']
        );
        $this->assertStringContainsString(
            'सडक विभाग',
            $result['matches'][0]['content']
        );
    }

    public function test_retriever_includes_general_public_source_for_any_office(): void
    {
        $source = \App\Models\ChatbotKnowledgeSource::create([
            'office_id' => null,
            'source_type' => 'file',
            'name' => 'General official source',
            'status' => 'indexed',
            'is_enabled' => true,
            'is_public' => true,
        ]);

        $document = \App\Models\ChatbotKnowledgeDocument::create([
            'source_id' => $source->id,
            'office_id' => null,
            'document_type' => 'document',
            'title' => 'General Office Policy',
            'extracted_text' => 'General public office policy information',
            'parser_status' => 'indexed',
        ]);

        \App\Models\ChatbotKnowledgeChunk::create([
            'document_id' => $document->id,
            'source_id' => $source->id,
            'office_id' => null,
            'chunk_number' => 1,
            'chunk_text' => 'General public office policy information',
            'token_count' => 8,
            'chunk_hash' => hash('sha256', 'General public office policy information'),
            'status' => 'active',
        ]);

        $result = app(\App\Services\Chatbot\ChatbotKnowledgeRetriever::class)
            ->search('general public office policy', 71);

        $this->assertCount(1, $result['matches']);
        $this->assertSame('General Office Policy', $result['matches'][0]['title']);
    }

    public function test_retriever_never_returns_another_offices_content(): void
    {
        $this->createRetrievalFixture(9, 'सडक विभाग कार्यालयको सूचना');

        $result = app(\App\Services\Chatbot\ChatbotKnowledgeRetriever::class)
            ->search('सडक विभाग', 4);

        $this->assertCount(0, $result['matches']);
    }

    public function test_retriever_excludes_private_disabled_unindexed_and_inactive_content(): void
    {
        $this->createRetrievalFixture(4, 'सडक विभाग private source', true, false);
        $this->createRetrievalFixture(4, 'सडक विभाग disabled source', false, true);
        $this->createRetrievalFixture(4, 'सडक विभाग pending document', true, true, 'pending');
        $this->createRetrievalFixture(4, 'सडक विभाग inactive chunk', true, true, 'indexed', 'inactive');

        $result = app(\App\Services\Chatbot\ChatbotKnowledgeRetriever::class)
            ->search('सडक विभाग', 4);

        $this->assertCount(0, $result['matches']);
    }


}
