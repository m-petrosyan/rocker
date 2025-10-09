<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EventNotificationDeleteJob implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct(protected object $event, protected object $user, protected int $msgId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->chat->deleteMessage($this->msgId)->send();

        $this->event->notifications()->detach();
    }
}
