<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;

class EventService
{
    use  ComponentServiceTrait;

    public function store($attributes): void
    {
        $event = auth()->user()->events()->create($attributes);

        if (isset($attributes['poster_file'])) {
            $this->addImage($event, $attributes['poster_file'], 'poster');
        }

        $this->addSyncBand($event, $attributes);
    }

    public function update($attributes, $event): void
    {
        $event->update($attributes);

        if (isset($attributes['poster_file'])) {
            $event->clearMediaCollection('poster');
            $this->addImage($event, $attributes['poster_file'], 'poster');
        }

        $this->addSyncBand($event, $attributes);
    }

    public function destroy($event): void
    {
        $event->status()->first()->update(['status' => 'deleted']);
//        $event->delete();
//        $event->clearMediaCollection('poster');
    }
}
