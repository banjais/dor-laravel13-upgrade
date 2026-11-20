<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 30)->default('global')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('bot_name', 100)->default('DoR Assistant');
            $table->text('welcome_message')->nullable();
            $table->text('offline_message')->nullable();
            $table->string('response_language', 10)->default('both');
            $table->string('position', 20)->default('bottom_right');
            $table->string('panel_color', 7)->default('#FFFFFF');
            $table->string('header_color', 7)->default('#142D4E');
            $table->string('user_message_color', 7)->default('#1769AA');
            $table->string('bot_message_color', 7)->default('#F1F4F7');
            $table->string('text_color', 7)->default('#263238');
            $table->string('ai_provider', 20)->default('openai');
            $table->string('ai_model', 100)->default('gpt-4o-mini');
            $table->text('api_key')->nullable();
            $table->boolean('voice_enabled')->default(false);
            $table->boolean('speech_input_enabled')->default(false);
            $table->boolean('voice_output_enabled')->default(false);
            $table->string('voice_type', 10)->default('female');
            $table->string('custom_voice', 100)->nullable();
            $table->decimal('voice_speed', 3, 2)->default(1.00);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_settings');
    }
};
