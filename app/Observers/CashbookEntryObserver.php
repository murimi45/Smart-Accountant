<?php

namespace App\Observers;

use App\Models\CashbookEntry;
use App\Services\LedgerService;

class CashbookEntryObserver
{
    public function created(CashbookEntry $entry): void
    {
        app(LedgerService::class)->postFromCashbookEntry($entry);
    }
}
