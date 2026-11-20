<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_theme_settings', function (Blueprint $table) {
            $table->id();

            $table->string('scope', 30)->default('global')->unique();

            $table->string('page_background', 7)->default('#F5F6F4');
            $table->string('card_background', 7)->default('#FFFFFF');
            $table->string('header_background', 7)->default('#142D4E');
            $table->string('nav_background', 7)->default('#1769AA');
            $table->string('accent_color', 7)->default('#8B2635');
            $table->string('footer_background', 7)->default('#263746');
            $table->string('text_color', 7)->default('#263238');
            $table->string('border_color', 7)->default('#E5E8E6');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_theme_settings');
    }
};
