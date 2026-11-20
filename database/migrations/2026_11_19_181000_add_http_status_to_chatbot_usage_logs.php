<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('chatbot_usage_logs', 'http_status')) {
            Schema::table('chatbot_usage_logs', function (Blueprint $table) {
                $table->unsignedSmallInteger('http_status')->nullable()->after('error_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chatbot_usage_logs', 'http_status')) {
            Schema::table('chatbot_usage_logs', function (Blueprint $table) {
                $table->dropColumn('http_status');
            });
        }
    }
};
