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

    public function show(Event $event): Response
    {
        $url = url()->current();

        return Inertia::render('Events/Event', [
            'event' => $event->load(['views']),
            'url' => $url,
        ]);
    }
}
