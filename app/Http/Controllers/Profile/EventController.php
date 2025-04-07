<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Event\EventCreateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
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
            $response = Http::timeout(10)
                ->retry(3, 1000)
                ->attach(
                    'poster_file',
                    file_get_contents($request->file('poster_file')->getRealPath()),
                    $request->file('poster_file')->getClientOriginalName()
                )
                ->post('https://bot.rocker.am/api/event', [
                    'rocker' => [
                        'username' => auth()->user()->username,
                        'admin' => 1,
                    ],
                    'title' => $request->title,
                    'content' => $request->content,
                    'type' => $request->type,
                    'country' => $request->country,
                    'location' => $request->location,
                    'genre' => $request->genre,
                    'price' => $request->price,
                    'start_date' => $request->start_date,
                    'start_time' => $request->start_time,
                    'cordinates[latitude]' => $request->cordinates['latitude'],
                    'cordinates[longitude]' => $request->cordinates['longitude'],
                ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed', $response->status());
            }

            // Успешный случай - редирект с сообщением
            return redirect()
                ->back()
                ->with('success', 'Event created successfully');
        } catch (\Exception $e) {
            \Log::error('Event creation failed: '.$e->getMessage(), [
                'status' => $response?->status(),
                'body' => $response?->body(),
                'request' => $request->validated(),
            ]);

            // Ошибка - редирект с сообщением об ошибке
            return redirect()
                ->back()
                ->with('error', 'Failed to create event: '.$e->getMessage());
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
