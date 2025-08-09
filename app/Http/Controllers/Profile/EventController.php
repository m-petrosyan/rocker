<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Event\EventCreateRequest;
use App\Repositories\BandRepository;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventController
{
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

    public function update()
    {
//        return Inertia::render('Events/Event', [
//            'event' => $response->json()['data'],
//        ]);
//        bot.rocker.loc
    }
}
