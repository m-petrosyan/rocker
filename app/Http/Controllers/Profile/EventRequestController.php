<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Event\NewEventRequest;
use App\Models\Event;
use App\Repositories\EventRepository;
use Inertia\Inertia;
use Inertia\Response;

class EventRequestController
{
    public function index()
    {
        return Inertia::render('Events/Events', [
            'events' => EventRepository::requestList(),
        ]);
    }

    public function show(Event $event): Response
    {
        return Inertia::render('Events/Event', [
            'event' => $event->load(['views']),
            'request' => true,
        ]);
    }

    public function update(Event $event, NewEventRequest $request)
    {
        $event->status()->first()->update($request->validated());
    }
}