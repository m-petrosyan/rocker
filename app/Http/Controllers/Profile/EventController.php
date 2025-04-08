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
        return Inertia::render('Profile/Events/EventCreateEdit');
    }


    public function store(EventCreateRequest $request): RedirectResponse
    {
        $response = $this->eventService->store($request);

        dd($response->body());

        return redirect()
            ->back()
            ->with('success', 'Событие успешно создано');
    }

    public function update()
    {
//        return Inertia::render('Events/Event', [
//            'event' => $response->json()['data'],
//        ]);
//        bot.rocker.loc
    }
}
