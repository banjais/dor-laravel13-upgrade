<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')
                ->nullable()
                ->constrained('chatbot_knowledge_sources')
                ->cascadeOnDelete();

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

            $table->index(['office_id', 'language_id', 'parser_status'], 'cb_docs_office_lang_status_idx');
            $table->index(['source_id', 'parser_status']);
            $table->index(['checksum']);
            $table->index(['source_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge_documents');
    }
};
