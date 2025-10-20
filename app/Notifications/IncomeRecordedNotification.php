<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncomeRecordedNotification extends Notification
{
    protected $studentName, $className, $amount;

    public function __construct($studentName, $className, $amount)
    {
        $this->studentName = $studentName;
        $this->className = $className;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'income',
            'title' => 'Payment Received',
            'message' => 'KES ' . number_format($this->amount) . ' received from ' . $this->studentName . ' – ' . $this->className,
        ];
    }

   
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
