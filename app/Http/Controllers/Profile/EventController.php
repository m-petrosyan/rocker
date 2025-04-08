<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Event\EventCreateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class EventController
{
    public function create(): Response
    {
        return Inertia::render('Profile/Events/EventCreateEdit');
    }


    public function store(EventCreateRequest $request): RedirectResponse
    {
        $response = null;

        try {
            $validated = $request->validated();
            $payload = [
                'rocker' => [
                    'username' => auth()->user()->username,
                    'admin' => 1,
                ],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'type' => $validated['type'],
                'country' => $validated['country'],
                'location' => $validated['location'],
                'genre' => $validated['genre'],
                'price' => $validated['price'] ?? null,
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'],
                'cordinates[latitude]' => $validated['cordinates']['latitude'] ?? null,
                'cordinates[longitude]' => $validated['cordinates']['longitude'] ?? null,
                'link' => $validated['link'] ?? null,
                'ticket' => $validated['ticket'] ?? null,
            ];

            $http = Http::timeout(10)
                ->retry(3, 1000)
                ->withHeaders(['Accept' => 'application/json']);

            if ($request->hasFile('poster_file') && $request->file('poster_file')->isValid()) {
                $file = $request->file('poster_file');
                $http->attach(
                    'poster_file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }

            $response = $http->post('https://bot.rocker.am/api/event', $payload);

            if (!$response->successful()) {
                throw new \Exception(
                    'API request failed: '.($response->json('message') ?? 'Unknown error'),
                    $response->status()
                );
            }

            return redirect()
                ->back()
                ->with('success', 'Event created successfully');
        } catch (\Exception $e) {
            Log::error('Event creation failed: '.$e->getMessage(), [
                'status' => $response?->status(),
                'body' => $response?->body(),
                'request' => $request->validated(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to create event: '.$e->getMessage())
                ->withInput();
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
