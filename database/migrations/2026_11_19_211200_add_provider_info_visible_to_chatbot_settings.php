<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            $table->boolean('provider_info_visible')->default(false)->after('supported_by_visible');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_settings', function (Blueprint $table) {
            $table->dropColumn('provider_info_visible');
        });
    }
};
