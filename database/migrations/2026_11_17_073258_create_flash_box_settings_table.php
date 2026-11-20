<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_box_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 30)->default('global')->unique();

            $table->boolean('animation_enabled')->default(true);
            $table->boolean('autoplay_enabled')->default(true);
            $table->unsignedSmallInteger('autoplay_speed')->default(4000);
            $table->string('animation_effect', 10)->default('slide');
            $table->boolean('pause_on_hover')->default(true);
            $table->boolean('arrows_enabled')->default(true);
            $table->boolean('dots_enabled')->default(false);
            $table->boolean('mobile_animation_enabled')->default(true);

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_box_settings');
    }
};
