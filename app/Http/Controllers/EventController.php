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
            'events' => EventRepository::eventsList(filters: request()->only(['country', 'from', 'to'])),
        ]);
    }

    public function show(Event $event): Response
    {
        views($event)->record();

        $url = url()->current();

        return Inertia::render('Events/Event', [
            'event' => $event->load(['views', 'bands', 'user:id,username']),
            'url' => $url,
        ]);
    }

    public function past()
    {
//        dd(request()->query('page', 1));

        return Inertia::render('Events/Events', [
            'events' => EventRepository::eventsList(
                limit: 52,
                page: request()->query('page', 1),
                past: true,
                filters: request()->only(['country', 'from', 'to'])
            ),
            'isPast' => true,
        ]);
    }
}
