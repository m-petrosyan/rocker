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
            $http = Http::timeout(10)
                ->throw()
                ->retry(3, 1000)
                ->withHeaders(['Accept' => 'application/json']);

            $payload = [
                'rocker[username]' => auth()->user()->username,
                'rocker[role]' => 'admin',
                'title' => $validated['title'],
                'content' => $validated['content'],
                'type' => $validated['type'],
                'country' => $validated['country'],
                'location' => $validated['location'],
                'coordinates[latitude]' => $validated['cordinates']['latitude'] ?? null,
                'coordinates[longitude]' => $validated['cordinates']['longitude'] ?? null,
                'genre' => $validated['genre'],
                'price' => $validated['price'] ?? null,
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'],
                'link' => $validated['link'] ?? null,
                'ticket' => $validated['ticket'] ?? null,
            ];

            if ($request->hasFile('poster_file') && $request->file('poster_file')->isValid()) {
                $file = $request->file('poster_file');
                $http->attach(
                    'poster_file',
                    file_get_contents($file->path()),
                    $file->getClientOriginalName()
                );
            }

            $response = $http->post('http://bot.rocker.loc/api/event', $payload);

            return redirect()
                ->back()
                ->with('success', 'Событие успешно создано');
        } catch (\Exception $e) {
            Log::error(
                $e->getMessage().' Не удалось создать событие: '.$e->getTraceAsString(),
                [
                    'status' => $response?->status(),
                    'body' => $response?->body(),
                ]
            );

            return redirect()
                ->back()
                ->with('error', 'Ошибка при создании события: '.$e->getMessage())
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
