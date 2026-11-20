<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code', 80)->unique();
            $table->string('provider_name', 150);
            $table->string('provider_type', 20)->default('external');
            $table->string('api_format', 40)->default('openai_compatible');
            $table->string('model', 150)->nullable();
            $table->text('base_url')->nullable();
            $table->longText('api_key')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_enabled')->default(false);
            $table->unsignedSmallInteger('timeout_seconds')->default(30);
            $table->unsignedTinyInteger('max_retries')->default(1);
            $table->string('health_status', 30)->default('unknown');
            $table->timestamp('last_health_check_at')->nullable();
            $table->timestamp('quota_reset_at')->nullable();
            $table->text('last_error')->nullable();
            $table->longText('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['is_enabled', 'priority']);
            $table->index(['provider_type', 'health_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_providers');
    }
};
