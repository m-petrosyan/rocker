<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use App\Notifications\EntityCreatedNotification;
use Illuminate\Support\Facades\Gate;

class EventObserver
{

    public function creating(Event $event): void
    {
        $cities = ['yerevan', 'tbilisi', 'gyumri', 'batumi', 'dilijan', 'kutaisi'];

        $lowercaseText = strtolower($event['location']);

        $eventCity = 'all';

        foreach ($cities as $city) {
            if (str_contains($lowercaseText, $city)) {
                $eventCity = $city;
                break;
            }
        }

        $event->city = $eventCity;
    }

    public function created(Event $event): void
    {
        $isApproved = Gate::allows('full-access');

        $event->status()->create(
            [
                'status' => $isApproved
                    ? EventStatusEnum::ACCEPTED->value
                    : EventStatusEnum::PENDING->value,
            ]
        );

        if (!$isApproved) {
            $event->user?->notify(
                new EntityCreatedNotification(
                    'event',
                    $event->title,
                    route('events.show', $event),
                    'pending'
                )
            );
        }
    }
}

