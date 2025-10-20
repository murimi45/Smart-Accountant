<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IncomeRecordedNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Accept a single array instead of 3 parameters.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Store as database notification
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Data stored in the database for Livewire dashboard
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'income',
            'title' => $this->data['title'] ?? 'Payment Received',
            'message' => $this->data['message'] ?? '',
        ];
    }
}
