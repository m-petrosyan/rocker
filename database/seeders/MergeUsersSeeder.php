<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class MergeUsersSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '4G');

        $data = Http::get('https://bot.rocker.am/api/users')->collect();

        $data->chunk(3)->each(function ($chunk) {
            $chunk->each(function ($item) {
                $this->command->info('Processing user: '.$item['id']);

                $username = filled($item['username']) ? $item['username'] : (Str::slug(
                        $item['first_name'] ?? 'user'
                    ).'_'.Str::random(
                        6
                    ).$item['id']);

                $user = User::create(
                    [
                        'username' => $username,
                        'name' => $item['first_name'].' '.$item['last_name'] ?? $username,
                        'password' => bcrypt(Str::random(12)),
                        'last_activity' => $item['last_activity'],
                        'created_at' => $item['created_at'] ?? now(),
                        'updated_at' => $item['updated_at'] ?? now(),
                    ],
                );

                $eventsCount = count($item['events']);
                $this->command->info('Processing user: '.$user['id'].' events: '.$eventsCount);
                collect($item['events'])->chunk(1)->each(function ($eventChunk) use ($user, $eventsCount) {
                    foreach ($eventChunk as $event) {
                        $this->command->info('Processing event: '.$event['id']);

                        $newEvent = $user->events()->create([
                            'title' => $event['title'],
                            'content' => $event['content'],
                            'link' => $event['link'] ?? null,
                            'ticket' => $event['ticket'] ?? null,
                            'price' => $event['price'] ?? null,
                            'country' => $event['country'] ?? null,
                            'city' => $event['city'] ?? null,
                            'genre' => $event['genre'] ?? null,
                            'location' => $event['location'] ?? null,
                            'cordinates' => $event['cordinates'] ?? null,
                            'type' => $event['type'] ?? null,
                            'start_date' => $event['start_date'] ?? null,
                            'start_time' => $event['start_time'] ?? null,
                            'notify_count' => $event['notify_count'] ?? 0,
                            'created_at' => $event['created_at'] ?? now(),
                            'updated_at' => $event['updated_at'] ?? now(),
                        ]);
//                        dd($event['media']);
                        $newEvent->addMediaFromUrl($event['media'][0]['original_url'])
                            ->toMediaCollection('poster');
                    }
                });
            });
        });
        dd('done');
    }
}
