<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBlockedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $reason)
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
        return (new MailMessage)
                    ->subject('Your Account Has Been Blocked')
                    ->line('We regret to inform you that your account has been blocked by an administrator.')
                    ->line('Reason for blocking:')
                    ->line($this->reason)
                    ->line('If you believe this is a mistake, please contact support.');
    }
}
