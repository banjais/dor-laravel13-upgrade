<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notice_search_indexes', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('notice_id');
            $table->unsignedInteger('office_id');
            $table->unsignedInteger('language_id');

            $table->text('notice_heading')->nullable();
            $table->string('office_name', 500)->nullable();
            $table->longText('normalized_text');

            $table->timestamps();

            $table->unique(
                ['notice_id', 'language_id'],
                'notice_search_notice_language_unique'
            );

            $table->index(
                ['office_id', 'language_id'],
                'notice_search_office_language_idx'
            );
        });

        Schema::create('notice_search_terms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('search_index_id');
            $table->unsignedInteger('language_id');
            $table->string('term', 191);

            $table->unique(
                ['search_index_id', 'term'],
                'notice_search_term_unique'
            );

            $table->index(
                ['language_id', 'term'],
                'notice_search_language_term_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_search_terms');
        Schema::dropIfExists('notice_search_indexes');
    }
};
