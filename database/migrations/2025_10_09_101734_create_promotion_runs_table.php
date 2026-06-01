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
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_term_id')->constrained('terms');
            $table->foreignId('to_term_id')->constrained('terms');
            $table->foreignId('promoted_by')->nullable()->constrained('users')->nullOnDelete();
             $table->enum('status', ['pending', 'running', 'completed', 'failed'])
             ->default('pending');
            $table->text('error_message')->nullable();
            $table->enum('type', ['term_promotion', 'class_promotion']);
            $table->string('active_key', 64)->nullable();
            $table->unique('active_key');
            $table->timestamps();

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
