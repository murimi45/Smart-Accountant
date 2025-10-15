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
        Schema::create('extra_fees', function (Blueprint $table) {
            $table->id();
            $table-> String('name');
            $table->decimal('amount',10,2);
            $table->boolean('is_quantity_based')->default(false);
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('term_id')->constrained('terms');
            $table->Year('year');
            $table->foreignId('school_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_fees');
    }
};
