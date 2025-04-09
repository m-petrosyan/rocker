<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function index(): array
    {
        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/event');
            $data = $response->json();
            Cache::put('events', $data, now()->addHour(2));
        } catch (\Exception $e) {
            if (Cache::has('events')) {
                $data = Cache::get('events');
            } else {
                $data = ['data' => [], 'error' => $e->getMessage()];
            }
        }

        return $data;
    }

    public function store($request)
    {
        $response = null;

        try {
            $validated = $request->validated();
            $http = Http::timeout(10)
                ->throw()
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

            $url = config(
                'app.env'
            ) === 'production' ? 'https://bot.rocker.am/api/event' : 'http://bot.rocker.loc/api/event';
         
            $response = $http->post($url, $payload);

            $data = json_decode($response->body(), true);

            auth()->user()->events()->create([
                'event_id' => $data['event_id'],
                'notify_count' => $data['notify_count'],
            ]);

            return $response;
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
}
