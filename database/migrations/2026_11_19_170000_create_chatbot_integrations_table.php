<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('integration_code', 80)->unique();
            $table->string('display_name', 150);
            $table->string('integration_type', 40);
            $table->longText('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->string('health_status', 30)->default('not_tested');
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->text('last_error')->nullable();
            // Match the existing pndc_users.id type: signed INT.
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('created_by')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')
                ->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_enabled', 'health_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_integrations');
    }
};
