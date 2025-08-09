<?php

namespace App\Events;

use App\Models\EventConfirm;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventConfirmUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public EventConfirm $eventConfirm;

    /**
     * Create a new event instance.
     */
    public function __construct(EventConfirm $eventConfirm)
    {
        $this->eventConfirm = $eventConfirm;
    }

}
