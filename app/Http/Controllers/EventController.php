<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Repositories\EventReoisutiry;
use App\Services\EventService;
use Illuminate\Support\Facades\Http;
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
            'events' => EventReoisutiry::eventsList(),
        ]);
    }

    public function show($eventId): Response
    {
        $response = Http::get('https://bot.rocker.am/api/event/'.$eventId);

        $event = Event::query()->where('event_id', $eventId)->first();

        $url = url()->current();

        if ($event) {
            views($event)->record();
        }

        return Inertia::render('Events/Event', [
            'event' => $response->json()['data'],
            'notify_count' => $event?->notify_count,
            'views' => $event?->views,
            'url' => $url,
        ]);
    }
}
