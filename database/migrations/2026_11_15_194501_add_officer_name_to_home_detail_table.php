<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_detail', function (Blueprint $table) {
            $table->string('officer_name')->nullable()->after('home_heading');
        });
    }

    public function down(): void
    {
        Schema::table('home_detail', function (Blueprint $table) {
            $table->dropColumn('officer_name');
        });
    }
};
