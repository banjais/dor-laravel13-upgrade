<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_answer_cache', function (Blueprint $table) {
            $table->id();
            $table->char('cache_key', 64)->unique();
            $table->char('question_hash', 64);
            $table->text('normalized_question');
            $table->string('language_code', 20);
            $table->unsignedBigInteger('office_id')->nullable();
            $table->longText('answer');
            $table->longText('source_references')->nullable();
            $table->char('index_version', 64);
            $table->boolean('is_public')->default(true);
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['office_id', 'language_code', 'is_public'], 'cb_cache_scope_idx');
            $table->index(['expires_at']);
            $table->index(['question_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_answer_cache');
    }
};
