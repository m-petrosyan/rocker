<?php

namespace App\Services;

use App\Notifications\NewCreationNotification;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Facades\Notification;

class EventService
{
    use  ComponentServiceTrait;

    public function store($attributes): void
    {
        $event = auth()->user()->events()->create($attributes);

        if (isset($attributes['poster_file'])) {
            $this->addImage($event, $attributes['poster_file'], 'poster');
        }

//        if (auth()->user()->isAdmin()) {
//            event(new EventConfirmUpdatedEvent($event->confirm));
//        }
//        if (Gate::allows('crud-access') || auth()->user()->role === 'organizer') {
//            if (config('app.env') === 'production') {
//                event(new EventConfirmUpdatedEvent($event->confirm));
//            }
//        } else {
//            dd('no');
//        }


        Notification::route('mail', config('mail.admin.address'))
            ->notify(new NewCreationNotification($event));
    }

    public function update($attributes, $event): void
    {
        $event->update($attributes);

        if (isset($attributes['poster_file'])) {
            $event->clearMediaCollection('poster');
            $this->addImage($event, $attributes['poster_file'], 'poster');
        }
    }

    public function destroy($event): void
    {
        $event->clearMediaCollection('poster');

        $event->delete();
    }
}
