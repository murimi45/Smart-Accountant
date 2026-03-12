<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use App\Notifications\SummaryNotification;

class SendDailySummary extends Command
{
    // The command signature used in the terminal
    protected $signature = 'summary:daily';

    // Description of the command
    protected $description = 'Send daily income and expense summary to admins';

    // This is the handle method, which Laravel runs when the command is executed
    public function handle()
    {
        $totalIncome = Payment::whereDate('created_at', today())->sum('amount');
        $totalExpense = Expense::whereDate('created_at', today())->sum('amount');

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new SummaryNotification([
                'title' => 'Daily Summary',
                'message' => 'Income: KES '.$totalIncome.' | Expense: KES '.$totalExpense.' (Today)',
            ]));
        }

        $this->info('Summary notifications sent!');
    }
}
