<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailySummaryRecordedNotification extends Notification
{
    use Queueable;

    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'summary',
            'title' => $this->data['title'] ?? 'Daily Summary',
            'message' => $this->data['message'] ?? '',
        ];
    }
}
