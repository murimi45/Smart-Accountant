<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_reminder_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->unsignedSmallInteger('min_days_outstanding')->default(7);
            $table->unsignedSmallInteger('reminder_interval_days')->default(7);
            $table->boolean('current_term_only')->default(true);
            $table->timestamps();
        });

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('student_id')->constrained()->nullOnDelete();
            $table->string('source', 32)->default('manual')->after('status');
            $table->index(['school_id', 'invoice_id', 'source'], 'sms_logs_school_invoice_source_index');
        });
    }

    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex('sms_logs_school_invoice_source_index');
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropColumn('source');
        });

        Schema::dropIfExists('fee_reminder_settings');
    }
};
