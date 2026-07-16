<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FacebookEventImportService;
use Illuminate\Console\Command;

class FetchFacebookEventsCommand extends Command
{
    protected $signature = 'app:fetch-facebook-events
        {--user= : User ID (limit to one user)}
        {--dry-run : Show what would be done without actually importing}';

    protected $description = 'Fetch new events from Facebook pages of all users';

    public function handle(FacebookEventImportService $service): int
    {
        $userId = $this->option('user');
        $dryRun = (bool) $this->option('dry-run');

        $query = User::query()->whereHas('facebookPages');

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->with('facebookPages', 'settings')->get();

        if ($users->isEmpty()) {
            $this->info('No users with connected Facebook pages found.');

            return self::SUCCESS;
        }

        $this->info("Users found: {$users->count()}");

        $totalImported = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($users as $user) {
            $this->line('');
            $this->line("👤 {$user->name} (@{$user->username})");

            foreach ($user->facebookPages as $fbPage) {
                $pageUrl = $fbPage->page_url;
                $this->line("   🔗 Page: {$pageUrl}");

                if ($dryRun) {
                    $this->line('   📄 Skipping (dry-run)');

                    continue;
                }

                $stats = $service->importForUrl($pageUrl, $user);

                $this->line("   📥 Imported: {$stats['imported']}");
                $this->line("   ⏭️  Skipped: {$stats['skipped']}");
                $this->line("   ❌ Errors: {$stats['errors']}");

                $totalImported += $stats['imported'];
                $totalSkipped += $stats['skipped'];
                $totalErrors += $stats['errors'];
            }
        }

        $this->newLine();
        $this->info("Done: created {$totalImported} events, skipped {$totalSkipped}, errors {$totalErrors}");

        return self::SUCCESS;
    }
}
