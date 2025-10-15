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
        Schema::create('student_histories', function (Blueprint $table) {
            $table->id();$table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_class_id')->nullable();
            $table->unsignedBigInteger('to_class_id')->nullable();
            $table->unsignedBigInteger('from_term_id')->nullable();
            $table->unsignedBigInteger('to_term_id')->nullable();
            $table->decimal('carried_balance', 10, 2)->default(0);
            $table->decimal('carried_credit', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['student_id', 'from_term_id', 'to_term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_histories');
    }
};
