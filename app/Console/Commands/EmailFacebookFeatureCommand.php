<?php

namespace App\Console\Commands;

use App\Models\Band;
use App\Models\User;
use App\Notifications\FacebookFeatureNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class EmailFacebookFeatureCommand extends Command
{
    protected $signature = 'app:email-facebook-feature
        {--dry-run : Show users without sending emails}';

    protected $description = 'Send Facebook import feature announcement to users who have at least one band';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Get unique email addresses of users who own at least one band
        $users = User::query()
            ->whereHas('bands')
            ->whereNotNull('email')
            ->select('id', 'name', 'email')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users with bands found.');

            return self::SUCCESS;
        }

        $this->info("Found {$users->count()} users with bands.");

        if ($dryRun) {
            $this->newLine();
            $this->line('Dry-run — would send to:');
            foreach ($users as $user) {
                $this->line("  • {$user->name} <{$user->email}>");
            }

            return self::SUCCESS;
        }

        $originalMailer = config('mail.default');
        $sent = 0;

        foreach ($users as $user) {
            try {
                Notification::route('mail', $user->email)
                    ->notify(new FacebookFeatureNotification);
                $sent++;
                $this->line("✅ Sent to: {$user->name} <{$user->email}>");
            } catch (\Throwable $e) {
                $this->error("❌ Failed for {$user->email}: {$e->getMessage()}");
            }
        }

        config(['mail.default' => $originalMailer]);

        $this->newLine();
        $this->info("Done! Sent {$sent} / {$users->count()} emails.");

        return self::SUCCESS;
    }
}
