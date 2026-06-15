<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->foreignId('school_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });

        $categories = DB::table('expense_categories')->orderBy('id')->get();

        foreach ($categories as $category) {
            $schoolId = DB::table('expenses')
                ->where('expense_category_id', $category->id)
                ->value('school_id');

            if ($schoolId) {
                DB::table('expense_categories')
                    ->where('id', $category->id)
                    ->update(['school_id' => $schoolId]);
            }
        }

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropIndex(['school_id']);
            $table->dropConstrainedForeignId('school_id');
        });
    }
};
