<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('office', 'office_instagram_page')) {
            Schema::table('office', function (Blueprint $table) {
                $table->string('office_instagram_page', 1000)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('office', 'office_instagram_page')) {
            Schema::table('office', function (Blueprint $table) {
                $table->dropColumn('office_instagram_page');
            });
        }
    }
};
