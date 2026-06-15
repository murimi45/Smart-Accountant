<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->string('provider_message_id')->nullable()->after('status');
            $table->index('provider_message_id', 'sms_logs_provider_message_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropIndex('sms_logs_provider_message_id_index');
            $table->dropColumn('provider_message_id');
        });
    }
};
