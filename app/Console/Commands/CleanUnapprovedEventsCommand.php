<?php

namespace App\Console\Commands;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use App\Models\EventStatus;
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
    protected $description = 'Reject events whose status has not been changed from pending for more than 2 days';

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

        if ($pendingEventIds->isEmpty()) {
            $this->info('No unapproved events found.');

            return Command::SUCCESS;
        }

        EventStatus::whereIn('event_id', $pendingEventIds)
            ->update([
                'status' => EventStatusEnum::REJECTED->value,
                'reason' => 'Auto-rejected: no action taken within 2 days',
            ]);

        $this->info("Rejected {$pendingEventIds->count()} unapproved event(s).");

        return Command::SUCCESS;
    }
}
