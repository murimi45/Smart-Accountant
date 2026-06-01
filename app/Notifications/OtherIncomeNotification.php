<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OtherIncomeNotification extends Notification
{
    use Queueable;

    protected $source, $description, $amount;

    public function __construct($source, $description, $amount)
    {
        $this->source = $source;
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
            'type' => 'other_income',
            'title' => 'Other Income Recorded',
            'message' => 'KES ' . number_format($this->amount) . ' received from ' . $this->source . ' (Other Income – ' . $this->description . ')',
        ];
    }
}
