<?php

namespace App\Console\Commands;

use App\Services\OverdueSmsReminderService;
use Illuminate\Console\Command;

class SendOverdueSmsReminders extends Command
{
    protected $signature = 'reminders:overdue-sms';

    protected $description = 'Send scheduled SMS reminders to parents with overdue fee balances';

    public function handle(OverdueSmsReminderService $service): int
    {
        $stats = $service->runAll();

        $this->info(sprintf(
            'Overdue SMS reminders: %d school(s), %d queued, %d skipped.',
            $stats['schools'],
            $stats['queued'],
            $stats['skipped']
        ));

        return self::SUCCESS;
    }
}
