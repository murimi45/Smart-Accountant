<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification
{
    use Queueable;

    protected $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'summary',
            'title' => 'Daily Fee Summary',
            'message' => 'A total of KES ' . number_format($this->amount) . ' was collected today',
        ];
    }
}
