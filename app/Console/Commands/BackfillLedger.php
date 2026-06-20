<?php

namespace App\Console\Commands;

use App\Models\CashbookEntry;
use App\Services\LedgerService;
use Illuminate\Console\Command;

class BackfillLedger extends Command
{
    protected $signature = 'ledger:backfill {--school= : Limit to a single school ID}';

    protected $description = 'Post ledger entries for existing cashbook transactions';

    public function handle(LedgerService $ledgerService): int
    {
        $schoolId = $this->option('school');

        $query = CashbookEntry::withoutGlobalScopes()->orderBy('id');
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $posted = 0;
        $skipped = 0;

        $query->each(function (CashbookEntry $entry) use ($ledgerService, &$posted, &$skipped) {
            if ($entry->ledgerEntries()->exists()) {
                $skipped++;

                return;
            }

            try {
                $ledgerService->postFromCashbookEntry($entry);
                if ($entry->ledgerEntries()->exists()) {
                    $posted++;
                }
            } catch (\Throwable $e) {
                $this->warn("Cashbook entry #{$entry->id}: {$e->getMessage()}");
            }
        });

        $this->info("Ledger backfill complete. Posted: {$posted}, skipped (already posted): {$skipped}.");

        return self::SUCCESS;
    }
}
