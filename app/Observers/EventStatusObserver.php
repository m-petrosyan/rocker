<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Jobs\EventNotificationJob;
use App\Models\EventStatus;
use Illuminate\Support\Facades\Gate;

class EventStatusObserver
{
    public function created(EventStatus $eventStatus): void
    {
        if (Gate::allows('full-access')) {
            dispatch(new EventNotificationJob($eventStatus->event));
        }
    }

    public function updated(EventStatus $eventStatus): void
    {
        if ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::ACCEPTED->value) {
            dispatch(new EventNotificationJob($eventStatus->event));
        }
    }
}
