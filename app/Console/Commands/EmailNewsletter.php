<?php

namespace App\Console\Commands;

use App\Models\Band;
use App\Notifications\NewsletterNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EmailNewsletter extends Command
{
    protected $signature = 'app:email-newsletter';
    protected $description = 'Send newsletter via Notification using bulk mailer';

    public function handle(): void
    {
        $emails = Band::query()
            ->whereNotNull('user_id')
            ->join('users', 'bands.user_id', '=', 'users.id')
//            ->whereNotIn('users.email', [
//                'sos.voskanyan@gmail.com',
//                'infernalrecordsarmenia@gmail.com',
//            ])
            ->pluck('users.email')
            ->unique();
//
//        $emails = ['miqayelpetrosyan@gmail.com'];

        $originalMailer = config('mail.default');
//        config(['mail.default' => 'bulk']);

        foreach ($emails as $email) {
            Notification::route('mail', $email)->notify(new NewsletterNotification());

            $this->info("Sent to: $email");
        }

        config(['mail.default' => $originalMailer]);
    }
}

