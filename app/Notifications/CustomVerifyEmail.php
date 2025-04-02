<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Random\RandomException;

class CustomVerifyEmail extends VerifyEmail
{
    protected User $user;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @throws RandomException
     */
    protected function verificationUrl($notifiable): array
    {
        $this->user = $notifiable;

        $hash = random_int(100000, 999999);

        $notifiable->verification()->create(
            [
                'user_id' => $this->user->id,
                'hash' => $hash,
                'expires_at' => now()->addMinutes(60),
            ]
        );

        return ['url' => config('app.fe').'/verification?code='.urlencode($hash), 'hash' => $hash];
    }


    protected function buildMailMessage($data): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Confirm Your Rocker Account!'))
            ->view('emails.verify-email', ['url' => $data['url'], 'code' => $data['hash'], 'user' => $this->user]);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return MailMessage
     * @throws RandomException
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return $this->buildMailMessage($verificationUrl);
    }
}
