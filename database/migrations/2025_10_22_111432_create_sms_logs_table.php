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
        Schema::create('sms_logs', function (Blueprint $table) {
            
            $table->id();
            $table->string('to');            
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, delivered, failed
            $table->text('response')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->timestamps();            
           // $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
