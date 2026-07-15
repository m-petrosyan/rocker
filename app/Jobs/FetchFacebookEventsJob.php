<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FacebookEventImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchFacebookEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(public User $user) {}

    public function handle(FacebookEventImportService $service): void
    {
        $totalImported = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($this->user->facebookPages as $fbPage) {
            $stats = $service->importForUrl($fbPage->page_url, $this->user);
            $totalImported += $stats['imported'];
            $totalSkipped += $stats['skipped'];
            $totalErrors += $stats['errors'];
        }

        Log::info('FetchFacebookEventsJob: completed', [
            'user_id' => $this->user->id,
            'imported' => $totalImported,
            'skipped' => $totalSkipped,
            'errors' => $totalErrors,
        ]);
    }
}
