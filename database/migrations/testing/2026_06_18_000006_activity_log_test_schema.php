<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
            $table->index('log_name');
            $table->index(['school_id', 'log_name', 'created_at'], 'activity_log_school_log_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
