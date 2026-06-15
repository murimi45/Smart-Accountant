<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $dupes = DB::table('payment_channels')
            ->select('identifier', DB::raw('COUNT(*) as total'))
            ->groupBy('identifier')
            ->having('total', '>', 1)
            ->get();

        if ($dupes->isNotEmpty()) {
            throw new RuntimeException(
                'Cannot add unique payment channel identifier: duplicate paybill/till numbers exist. Each shortcode must map to one school.'
            );
        }

        Schema::table('payment_channels', function (Blueprint $table) {
            $table->unique('identifier', 'payment_channels_identifier_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payment_channels', function (Blueprint $table) {
            $table->dropUnique('payment_channels_identifier_unique');
        });
    }
};
