<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Notifications\IncomeRecordedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPaymentNotification implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Invoice $invoice;
    protected float $amount;

    public function __construct(Invoice $invoice, float $amount)
    {
        $this->invoice = $invoice;
        $this->amount = $amount;
    }

    public function handle()
    {
        // Notify all relevant users: admin + accountants
        $users = $this->invoice->school->users()->whereIn('role', ['admin', 'accountant'])->get();

        foreach ($users as $user) {
            $user->notify(new IncomeRecordedNotification([
                'title' => 'Payment Received',
                'message' => 'You have received KES ' . number_format($this->amount) .
                             ' from ' . $this->invoice->student->name .
                             ' for invoice #' . $this->invoice->id . '.',
            ]));
        }
    }
}
