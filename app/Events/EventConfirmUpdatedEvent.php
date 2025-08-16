<?php

namespace App\Events;

use App\Models\EventStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventConfirmUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EventStatus $eventStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(EventStatus $eventStatus)
    {
        $this->eventStatus = $eventStatus;
    }

}
