<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circular_admin_email_otps', function (Blueprint $table) {
            $table->id();

            $table->string('email_address', 100);
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('requested_by');

            $table->string('otp_hash', 255);
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);

            $table->timestamp('verified_at')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index(['email_address', 'requested_by']);
            $table->index('expires_at');
            $table->index('office_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circular_admin_email_otps');
    }
};
