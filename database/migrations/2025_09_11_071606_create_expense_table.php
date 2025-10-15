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
        Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('school_id'); // tenant
        $table->unsignedBigInteger('expense_category_id')->nullable();
        $table->string('description')->nullable();
        $table->decimal('amount', 15, 2);
        $table->string('payment_method')->nullable(); // cash, mpesa, bank...
        $table->date('expense_date')->nullable();
        $table->foreignId('term_id')->constrained('terms');
        $table->Year('year');
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();
        $table->softDeletes();

        $table->index('school_id');
        $table->index('expense_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
