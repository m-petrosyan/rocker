<?php

namespace App\Console\Commands;

use App\Notifications\NewsletterNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EmailNewsletter extends Command
{
    protected $signature = 'app:email-newsletter {emails*}';
    protected $description = 'Send newsletter via Notification using bulk mailer';

    public function handle(): void
    {
        $emails = $this->argument('emails');

        // временно подменим mailer
        $originalMailer = config('mail.default');
        config(['mail.default' => 'bulk']);

        foreach ($emails as $email) {
            Notification::route('mail', $email)->notify(new NewsletterNotification());
            $this->info("Sent to: $email");
        }

        // вернём обратно mailer
        config(['mail.default' => $originalMailer]);
    }
}

