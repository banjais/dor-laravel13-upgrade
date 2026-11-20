<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                ->constrained('chatbot_knowledge_documents')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->unsignedInteger('chunk_number');
            $table->longText('chunk_text');
            $table->unsignedInteger('token_count')->default(0);
            $table->char('chunk_hash', 64);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['document_id', 'chunk_number'], 'cb_chunks_document_number_unique');
            $table->index(['office_id', 'language_id', 'status'], 'cb_chunks_office_lang_status_idx');
            $table->index(['source_id']);
            $table->index(['chunk_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge_chunks');
    }
};
