<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification
{
    public function via(object $notifiable): array
    {
        dd($notifiable);

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Photographer for events")
            ->greeting(
                "You can add us to the guest list, we’ll arrange for a photographer to attend and capture the concert 📸"
            )
            ->action("For all questions write @mpetrosyan1", url('https://t.me/mpetrosyan1'));
    }
}
