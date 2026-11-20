<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office', function (Blueprint $table) {
            $table->string('office_alternative_email', 254)->nullable();
            $table->string('office_facebook_page', 1000)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('office', function (Blueprint $table) {
            $table->dropColumn([
                'office_alternative_email',
                'office_facebook_page',
            ]);
        });
    }
};
