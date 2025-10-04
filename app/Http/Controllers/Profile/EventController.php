<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventCreateRequest;
use App\Http\Requests\Event\EventUpdateRequest;
use App\Models\Event;
use App\Repositories\BandRepository;
use App\Services\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected EventService $eventService)
    {
    }

    public function create(): Response
    {
        return Inertia::render('Profile/Events/EventCreateEdit', [
            'bandsList' => BandRepository::bandNamesList(),
        ]);
    }


    public function store(EventCreateRequest $request): RedirectResponse
    {
        dd($request->validated());
        $this->eventService->store($request->validated());

//        session()->flash('message', $response['message']);

        return redirect()->route('profile.index')
            ->with(
                'success',
                auth()->user()->role === 'user'
                    ? 'Thank you, the event has been created.<br> The event will be added to the list after moderation'
                    : 'Thank you, the event has been created.'
            );
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $this->eventService->update($request->validated(), $event);
//        return Inertia::render('Events/Event', [
//            'event' => $response->json()['data'],
//        ]);
//        bot.rocker.loc
    }


    public function edit(Event $event): Response
    {
        return Inertia::render('Profile/Events/EventCreateEdit', [
            'event' => $event->load(['bands', 'media']),
            'bandsList' => BandRepository::bandNamesList(),
        ]);
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $this->eventService->destroy($event);

        return redirect()->route('profile.index')
            ->with('success', 'The event has been deleted.');
    }
}
