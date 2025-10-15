<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_runs', function (Blueprint $table) {
            $table->id();
            $table->decimal('school_id');
            $table->decimal('from_term_id');
            $table->decimal('to_term_id');
            $table->decimal('promoted_by')->nullable();
            $table->enum('type', ['term_promotion', 'class_promotion']);
            $table->timestamps();

            // Indexes and foreign keys
            $table->index(['school_id', 'from_term_id', 'to_term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_runs');
    }
};
