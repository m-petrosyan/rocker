<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use App\Notifications\NewCreationNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class EventObserver
{
    public function created(Event $event): void
    {
        $event->status()->create(
            [
                'status' => Gate::allows('full-access')
                    ? EventStatusEnum::ACCEPTED->value
                    : EventStatusEnum::PENDING->value,
            ]
        );

        if (config('app.env') === 'production') {
            Notification::route('mail', config('mail.admin.address'))
                ->notify(new NewCreationNotification($event));
        }
    }
}
