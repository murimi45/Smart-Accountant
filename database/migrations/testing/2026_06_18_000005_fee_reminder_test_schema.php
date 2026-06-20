<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('to');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->string('source', 32)->default('manual');
            $table->text('response')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fee_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->unsignedSmallInteger('min_days_outstanding')->default(7);
            $table->unsignedSmallInteger('reminder_interval_days')->default(7);
            $table->boolean('current_term_only')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_reminder_settings');
        Schema::dropIfExists('sms_logs');
    }
};
