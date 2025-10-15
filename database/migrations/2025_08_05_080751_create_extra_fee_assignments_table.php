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
        Schema::create('extra_fee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('extra_fee_id')->constrained('extra_fees');
            
            // Quantity and amount
            $table->integer('quantity'); 
            $table->decimal('amount', 10, 2); 
    
            // Multi-tenancy & tracking
            $table->foreignId('school_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
              $table->unique(['student_id', 'extra_fee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_fee_assignments');
    }
};
