<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_identity_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 30)->default('global')->unique();
            $table->string('government_name_np')->default('नेपाल सरकार');
            $table->string('government_name_en')->default('Government of Nepal');
            $table->string('ministry_name_np')->default('पूर्वाधार विकास मन्त्रालय');
            $table->string('ministry_name_en')->default('Ministry of Infrastructure Development');
            $table->string('department_name_np')->default('सडक विभाग');
            $table->string('department_name_en')->default('Department of Roads');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_identity_settings');
    }
};
