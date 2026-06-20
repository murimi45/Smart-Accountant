<?php

namespace App\Console\Commands;

use App\Models\Schools;
use Database\Seeders\AccountsTableSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAccounts extends Command
{
    protected $signature = 'accounts:backfill {--school= : Limit to a single school ID}';

    protected $description = 'Seed the default chart of accounts for schools that lack one';

    public function handle(): int
    {
        $schoolId = $this->option('school');

        $query = Schools::query();
        if ($schoolId) {
            $query->whereKey($schoolId);
        }

        $count = 0;

        $query->orderBy('id')->each(function (Schools $school) use (&$count) {
            $before = DB::table('accounts')->where('school_id', $school->id)->count();
            (new AccountsTableSeeder)->run($school->id);
            $after = DB::table('accounts')->where('school_id', $school->id)->count();

            if ($after > $before) {
                $count++;
                $this->line("Seeded accounts for school #{$school->id} ({$school->school_name})");
            }
        });

        $this->info("Done. {$count} school(s) received default accounts.");

        return self::SUCCESS;
    }
}
