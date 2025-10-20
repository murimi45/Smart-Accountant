<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseRecordedNotification extends Notification
{
    use Queueable;

    protected $description, $amount;

    public function __construct($description, $amount)
    {
        $this->description = $description;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'expense',
            'title' => 'Expense Recorded',
            'message' => 'KES ' . number_format($this->amount) . ' spent on ' . $this->description,
        ];
    }
}
