<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseRecordedNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Notification will be stored in database only
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Data that is stored in the database and read by Livewire
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'expense',
            'title' => $this->data['title'] ?? 'Expense Recorded',
            'message' => $this->data['message'] ?? '',
        ];
    }
}
