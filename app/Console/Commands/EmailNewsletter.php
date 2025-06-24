<?php

namespace App\Console\Commands;

use App\Notifications\NewsletterNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EmailNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:email-newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $recipients = ['miqayel@inbox.ru', 'miqayelpetrosyan@gmail.com'];

        foreach ($recipients as $email) {
            Mail::mailer('bulk')->to($email)->send(new NewsletterNotification());
        }
    }
}
