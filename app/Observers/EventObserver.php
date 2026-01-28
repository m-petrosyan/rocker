<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Models\Event;
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
        $event->status()->create(
            [
                'status' => Gate::allows('full-access')
                    ? EventStatusEnum::ACCEPTED->value
                    : EventStatusEnum::PENDING->value,
            ]
        );


    }
}
