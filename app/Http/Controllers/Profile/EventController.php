<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Event\EventCreateRequest;
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
                'bandsList' => BandRepository::bandList(),
            ]);
    }


    public function store(EventCreateRequest $request): RedirectResponse
    {
        try {
            $response = $this->eventService->store($request);

            session()->flash('message', $response['message']);

            return redirect()->route('profile.index')
                ->with(
                    'success',
                    'Thank you, the event has been created.<br> The event will be added to the list after moderation'
                );
        } catch (\Throwable $e) {
            session()->flash('message', $e->getMessage()['message'] ?? 'An error occurred while creating the event.');

            return redirect()->back()->withInput();
        }
    }

    public function update()
    {
//        return Inertia::render('Events/Event', [
//            'event' => $response->json()['data'],
//        ]);
//        bot.rocker.loc
    }
}
