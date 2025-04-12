<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
//        $gallery = Gallery::query()->find(1);
//        views($gallery)->record();
//
//        dd($gallery->viewsCount());

//        Cache::delete('events');

        $data = $this->eventService->index();

        return Inertia::render('Events/Events', [
            'events' => $data,
        ]);
    }

    public function show($eventId): Response
    {
        $response = Http::get('https://bot.rocker.am/api/event/'.$eventId);

        $event = Event::query()->where('event_id', $eventId)->first();

        if ($event) {
            views($event)->record();
        }

        return Inertia::render('Events/Event', [
            'event' => $response->json()['data'],
            'notify_count' => $event?->notify_count ?? 0,
            'views' => $event?->viewsCount() ?? 0,
        ]);
    }
}
