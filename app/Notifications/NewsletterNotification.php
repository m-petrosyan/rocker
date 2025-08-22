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
            ->subject("Keep your band profile fresh – add new content")
            ->greeting("Hi there! 🎸")
            ->line("We’d love to see your band page up to date!")
            ->line("To make it more engaging for your fans, please take a moment to update your page with:")
            ->line("- **External links** (official website, social media, streaming platforms)")
            ->line("- **Band description** including who plays which instrument")
            ->line("- **Photos** of the band members")
            ->line("- **YouTube videos** of your performances or clips")
            ->line("- **Albums** – we’ve just added this brand-new feature! 🎶")
            ->line(
                '[![Band Page](https://rocker.am/images/temp/band_page_example.jpg)](https://rocker.am/bands/avarayr)'
            )
            ->action("Update your band page", url('https://rocker.am'))
            ->salutation("— The Rocker Team");
    }
}
