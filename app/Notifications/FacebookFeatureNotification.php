<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacebookFeatureNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New feature: Auto-import events from your Facebook page')
            ->greeting('Hey there! 🎸')
            ->line('Great news — we\'ve just added a powerful new feature to **Rocker.am**!')
            ->line('Now you can connect your Facebook page to Rocker, and we\'ll **automatically import your events** every day.')
            ->line('No more manual copying & pasting — just link your page once, and your events appear on Rocker automatically.')
            ->line('Here\'s how it works:')
            ->line('1. Go to your **profile settings** on Rocker')
            ->line('2. Click **"Connect Facebook page"**')
            ->line('3. Enter your Facebook page URL')
            ->line('4. That\'s it! New events will be imported daily.')
            ->line('You can connect up to **3 different Facebook pages** to a single Rocker account.')
            ->action('Try it now', url('https://rocker.am/profile'))
            ->line('If you have any questions, just reply to this email.')
            ->salutation('— The Rocker Team');
    }
}
