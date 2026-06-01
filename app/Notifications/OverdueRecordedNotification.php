<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OverdueFeesNotification extends Notification
{
    use Queueable;

    protected $count;

    public function __construct($count)
    {
        $this->count = $count;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'overdue',
            'title' => 'Payment Overdue Alert',
            'message' => $this->count . ' students have overdue fee payments',
        ];
    }
}
