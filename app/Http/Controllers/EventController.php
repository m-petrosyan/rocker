<?php

namespace App\Http\Controllers;

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
        $url = url()->current();

        $event = EventRepository::get($eventId);


        return Inertia::render('Events/Event', [
            'event' => $event,
            'notify_count' => $event['notify_count'] ?? null,
            'views' => $event['views'] ?? null,
            'url' => $url,
        ]);
    }

    public function past()
    {
//        dd(request()->query('page', 1));

        //$page = request()->query('page', 1);
        return Inertia::render('Events/Events', [
            'events' => EventRepository::eventsList(limit: 52, page: request()->query('page', 1), past: true),
        ]);
    }
}
