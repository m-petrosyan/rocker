<?php

namespace App\Services;

use App\Notifications\NewCreationNotification;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class EventService
{
    use  ComponentServiceTrait;

    public function store($attributes)
    {
        try {
            $http = Http::timeout(10)
                ->throw()
                ->withHeaders(['Accept' => 'application/json']);

            $payload = [
                'rocker[username]' => auth()->user()->username,
                'rocker[role]' => auth()?->user()->role,
                'title' => $attributes['title'],
                'content' => $attributes['content'],
                'type' => $attributes['type'],
                'country' => $attributes['country'],
                'location' => $attributes['location'],
                'cordinates[latitude]' => $attributes['cordinates']['latitude'] ?? null,
                'cordinates[longitude]' => $attributes['cordinates']['longitude'] ?? null,
                'genre' => $attributes['genre'],
                'price' => $attributes['price'] ?? null,
                'start_date' => $attributes['start_date'],
                'start_time' => $attributes['start_time'],
                'link' => $attributes['link'] ?? null,
                'ticket' => $attributes['ticket'] ?? null,
            ];

            $request = request();
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


            $event = auth()->user()->events()->create([
                'event_id' => $data['event_id'],
                'notify_count' => $data['notify_count'],
            ]);

            $this->addSyncBand($event, $attributes);

//            Cache::forget('events');

            Notification::route('mail', config('mail.admin.address'))
                ->notify(new NewCreationNotification($event));

            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Unable to create event: '.$e->getMessage());
        }
    }
}
