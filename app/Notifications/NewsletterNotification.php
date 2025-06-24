<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification implements \Illuminate\Contracts\Mail\Mailable
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $table = $this->model->getTable();

        return (new MailMessage)
            ->subject("Photographer for events")
            ->greeting(
                "You can add us to the guest list, we’ll arrange for a photographer to attend and capture the concert 📸"
            )
            ->action("For all questions write @mpetrosyan1", url('https://t.me/mpetrosyan1'));
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
