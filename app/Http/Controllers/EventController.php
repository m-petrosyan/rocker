<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Repositories\EventRepository;
use App\Services\EventService;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Events/Events', [
            'events' => EventRepository::eventsList(),
        ]);
    }

    public function show($eventId): Response
    {
        $event = Event::query()->where('event_id', $eventId)->first();

        $url = url()->current();

        if ($event) {
            views($event)->record();
        }

        return Inertia::render('Events/Event', [
//            'event' => EventRepository::get($eventId),
            'event' => Event::find($eventId)->load('bands'),
            'notify_count' => $event?->notify_count,
            'views' => $event?->views,
            'url' => $url,
        ]);
    }
}
