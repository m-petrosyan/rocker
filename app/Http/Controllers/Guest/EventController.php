<?php

namespace App\Http\Controllers\Guest;

use App\Enums\EventStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Repositories\EventRepository;
use App\Services\EventService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService) {}

    public function index(): Response
    {
        return Inertia::render('Events/Events', [
            'events' => EventRepository::eventsList(filters: request()->only(['country', 'from', 'to'])),
        ]);
    }

    public function show(Event $event): Response
    {
        $this->authorizeRejectedEvent($event);

        views($event)->record();

        return Inertia::render('Events/Event', [
            'event' => $event->load(['views', 'bands', 'user:id,username']),
        ]);
    }

    public function past()
    {
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

    private function authorizeRejectedEvent(Event $event): void
    {
        $status = $event->status?->status;

        if ($status !== EventStatusEnum::REJECTED->value) {
            return;
        }

        $user = auth()->user();

        if (! $user || ($user->id !== $event->user_id && ! Gate::allows('full-access'))) {
            abort(404);
        }
    }
}
