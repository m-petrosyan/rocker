<?php

namespace App\Services;

use App\Events\EventConfirmUpdatedEvent;
use App\Events\EventRequestNotifyEvent;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Facades\Gate;

class EventService
{
    use  ComponentServiceTrait;

    public function store($attributes)
    {
//        dd($attributes);
//        dd(auth()->user()->role);

        $event = auth()->user()->events()->create($attributes);

        if (Gate::allows('crud-access') || auth()->user()->role === 'organizer') {
            if (config('app.env') === 'production') {
                event(new EventConfirmUpdatedEvent($event->confirm));
            }
        } else {
            dd('no');
        }
//        if ((new PermissionGate)->dashboardFullAccess()->get()) {
//            if (config('app.env') === 'production') {
//                event(new EventConfirmUpdatedEvent($event->confirm));
//            }
//        } else {
//            event(new EventRequestNotifyEvent());
//        }


    }
}
