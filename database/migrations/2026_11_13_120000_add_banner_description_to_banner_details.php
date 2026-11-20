<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_detail', function (Blueprint $table) {
            $table->longText('banner_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('banner_detail', function (Blueprint $table) {
            $table->dropColumn('banner_description');
        });
    }
};
