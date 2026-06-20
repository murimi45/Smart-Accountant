<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_waivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained('invoice_items')->nullOnDelete();
            $table->string('target_description')->nullable();
            $table->enum('scope', ['line', 'invoice']);
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('value', 10, 2);
            $table->decimal('computed_amount', 10, 2)->nullable();
            $table->string('reason', 500);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('review_notes', 500)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status']);
            $table->index(['invoice_id', 'status']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('invoice_waiver_id')
                ->nullable()
                ->after('invoice_id')
                ->constrained('invoice_waivers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_waiver_id');
        });

        Schema::dropIfExists('invoice_waivers');
    }
};
