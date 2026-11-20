<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing VARCHAR URL columns are converted to TEXT to reduce row size.
        $connection = Schema::getConnection();
        $grammar = $connection->getQueryGrammar();

        foreach (['primary_base_url', 'secondary_base_url'] as $column) {
            if (Schema::hasColumn('chatbot_settings', $column)) {
                $connection->statement(
                    'ALTER TABLE ' . $grammar->wrapTable('chatbot_settings') .
                    ' MODIFY ' . $grammar->wrap($column) . ' TEXT NULL'
                );
            }
        }

        foreach (['primary', 'secondary', 'fallback'] as $slot) {
            $columns = [
                $slot . '_enabled' => fn (Blueprint $table) =>
                    $table->boolean($slot . '_enabled')->default(false),
                $slot . '_provider' => fn (Blueprint $table) =>
                    $table->string($slot . '_provider', 30)->nullable(),
                $slot . '_model' => fn (Blueprint $table) =>
                    $table->string($slot . '_model', 150)->nullable(),
                $slot . '_base_url' => fn (Blueprint $table) =>
                    $table->text($slot . '_base_url')->nullable(),
                $slot . '_api_key' => fn (Blueprint $table) =>
                    $table->text($slot . '_api_key')->nullable(),
            ];

            foreach ($columns as $name => $addColumn) {
                if (!Schema::hasColumn('chatbot_settings', $name)) {
                    Schema::table('chatbot_settings', function (Blueprint $table) use ($addColumn) {
                        $addColumn($table);
                    });
                }
            }
        }

        $cacheColumns = [
            'external_api_enabled' => fn (Blueprint $table) =>
                $table->boolean('external_api_enabled')->default(false),
            'cache_enabled' => fn (Blueprint $table) =>
                $table->boolean('cache_enabled')->default(true),
            'cache_exact_match_enabled' => fn (Blueprint $table) =>
                $table->boolean('cache_exact_match_enabled')->default(true),
            'cache_ttl_minutes' => fn (Blueprint $table) =>
                $table->unsignedInteger('cache_ttl_minutes')->default(1440),
        ];

        foreach ($cacheColumns as $name => $addColumn) {
            if (!Schema::hasColumn('chatbot_settings', $name)) {
                Schema::table('chatbot_settings', function (Blueprint $table) use ($addColumn) {
                    $addColumn($table);
                });
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'external_api_enabled',
            'primary_enabled', 'primary_provider', 'primary_model',
            'primary_base_url', 'primary_api_key',
            'secondary_enabled', 'secondary_provider', 'secondary_model',
            'secondary_base_url', 'secondary_api_key',
            'fallback_enabled', 'fallback_provider', 'fallback_model',
            'fallback_base_url', 'fallback_api_key',
            'cache_enabled', 'cache_exact_match_enabled', 'cache_ttl_minutes',
        ];

        $existing = array_values(array_filter(
            $columns,
            fn ($column) => Schema::hasColumn('chatbot_settings', $column)
        ));

        if ($existing) {
            Schema::table('chatbot_settings', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
