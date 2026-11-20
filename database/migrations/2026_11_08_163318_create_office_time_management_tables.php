<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_time_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->default(4);
            $table->string('rule_name', 100);
            $table->unsignedTinyInteger('start_bs_month');
            $table->unsignedTinyInteger('start_bs_day');
            $table->unsignedTinyInteger('end_bs_month');
            $table->unsignedTinyInteger('end_bs_day');
            $table->time('opens_at');
            $table->time('closes_at');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['office_id', 'is_active']);
        });

        Schema::create('office_time_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->default(4);
            $table->enum('exception_type', [
                'special_schedule',
                'public_holiday',
                'emergency_closure',
            ]);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('all_day')->default(true);
            $table->boolean('until_further_notice')->default(false);
            $table->text('reason')->nullable();
            $table->string('decision_reference', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(
                ['office_id', 'exception_type', 'is_active'],
                'office_time_exception_type_idx'
            );
            $table->index(['effective_from', 'effective_to']);
        });

        Schema::create('office_time_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->default(4);
            $table->unsignedBigInteger('exception_id')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('action', 50);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->useCurrent();

            $table->index(['office_id', 'changed_at']);
            $table->index('exception_id');
        });

        DB::table('office_time_rules')->insert([
            [
                'office_id' => 4,
                'rule_name' => 'Winter Office Hours',
                'start_bs_month' => 8,
                'start_bs_day' => 16,
                'end_bs_month' => 11,
                'end_bs_day' => 15,
                'opens_at' => '09:00:00',
                'closes_at' => '16:00:00',
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'office_id' => 4,
                'rule_name' => 'Regular Office Hours',
                'start_bs_month' => 11,
                'start_bs_day' => 16,
                'end_bs_month' => 8,
                'end_bs_day' => 15,
                'opens_at' => '09:00:00',
                'closes_at' => '17:00:00',
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('office_time_change_logs');
        Schema::dropIfExists('office_time_exceptions');
        Schema::dropIfExists('office_time_rules');
    }
};
