<?php

namespace App\Console\Commands;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use Illuminate\Console\Command;

class CleanUnapprovedEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-unapproved-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete events whose status has not been changed from pending (not approved)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendingEventIds = Event::query()
            ->whereHas('status', function ($query) {
                $query->where('status', EventStatusEnum::PENDING->value);
            })
            ->where('created_at', '<', now()->subDays(2))
            ->pluck('id');

        $count = $pendingEventIds->count();

        if ($count === 0) {
            $this->info('No unapproved events found.');

            return Command::SUCCESS;
        }

        Event::whereIn('id', $pendingEventIds)->delete();

        $this->info("Deleted {$count} unapproved event(s).");

        return Command::SUCCESS;
    }
}
