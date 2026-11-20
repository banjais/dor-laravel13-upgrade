<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->nullable()
                ->constrained('chatbot_providers')
                ->nullOnDelete();

            $table->unsignedBigInteger('office_id')->nullable();
            $table->uuid('request_id')->nullable();
            $table->string('provider_slot', 20)->nullable();
            $table->string('model', 150)->nullable();
            $table->string('status', 30);
            $table->string('error_code', 80)->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->decimal('estimated_cost', 12, 6)->nullable();
            $table->char('request_hash', 64)->nullable();
            $table->text('safe_error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['provider_id', 'created_at']);
            $table->index(['office_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_usage_logs');
    }
};
