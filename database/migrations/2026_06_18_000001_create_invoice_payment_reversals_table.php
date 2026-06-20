<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payment_reversals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('reason', 500);
            $table->foreignId('reversed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('reversed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payment_reversals');
    }
};
