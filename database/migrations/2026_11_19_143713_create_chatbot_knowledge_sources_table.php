<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->index(['office_id', 'is_enabled', 'status'], 'cb_sources_office_status_idx');
            $table->index(['source_type', 'status']);
            $table->index(['external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge_sources');
    }
};
