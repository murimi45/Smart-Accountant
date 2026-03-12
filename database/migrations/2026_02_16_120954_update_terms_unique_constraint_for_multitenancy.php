<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            // Drop the old unique index
            $table->dropUnique('terms_name_year_unique'); // Laravel auto-names index as table_column_column_unique

            // Add new unique index including school_id
            $table->unique(['school_id', 'name', 'year'], 'terms_school_name_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
            // Drop the new index
            $table->dropUnique('terms_school_name_year_unique');

            // Restore the old index
            $table->unique(['name', 'year'], 'terms_name_year_unique');
        });
    }
};
