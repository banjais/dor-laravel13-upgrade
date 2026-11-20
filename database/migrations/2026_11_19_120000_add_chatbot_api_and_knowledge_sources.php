<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            $table->string('api_base_url', 2048)->nullable();
            $table->string('api_format', 30)->default('openai_compatible');
            $table->string('appsheet_url', 2048)->nullable();
            $table->string('google_sheet_url', 2048)->nullable();
            $table->string('website_base_url', 2048)->default('https://dor.gov.np');
            $table->boolean('website_index_enabled')->default(true);

            // Internal DoR AI and hardware configuration
            $table->boolean('internal_ai_enabled')->default(false);
            $table->string('hardware_mode', 10)->default('cpu');
            $table->boolean('gpu_enabled')->default(false);
            $table->string('gpu_server_url', 2048)->nullable();
            $table->string('gpu_model', 100)->nullable();

            // Public chatbot attribution (hidden by default)
            $table->string('supported_by_name', 150)->nullable();
            $table->string('supported_by_email', 254)->nullable();
            $table->boolean('supported_by_visible')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            $table->dropColumn([
                'api_base_url',
                'api_format',
                'appsheet_url',
                'google_sheet_url',
                'website_base_url',
                'website_index_enabled',
                'internal_ai_enabled',
                'hardware_mode',
                'gpu_enabled',
                'gpu_server_url',
                'gpu_model',
                'supported_by_name',
                'supported_by_email',
                'supported_by_visible',
            ]);
        });
    }
};
