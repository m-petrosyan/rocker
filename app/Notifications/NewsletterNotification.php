<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("We'd love to photograph your upcoming concert 🎤📸")
            ->greeting(
                "We’d love to support your upcoming performance!
If you add us to the guest list, we’ll send a professional photographer to cover the event — and share the photos with you afterward.
It’s free of charge and helps showcase your concert on our platform.
 For any questions, feel free to reach out:"
            )
            ->action("@mpetrosyan1", url('https://t.me/mpetrosyan1'));
    }
}
