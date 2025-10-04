<?php

namespace Database\Seeders;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use App\Models\User;
use App\Models\UserBot;
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

                $role = match ($item['role_id']) {
                    1 => 'admin',
                    2 => 'moderator',
                    default => 'user',
                };

                $user->assignRole($role);

                UserBot::withoutEvents(function () use ($user, $item) {
                    $user->chat()->create([
                        'chat_id' => $item['chat']['chat_id'],
                        'name' => $item['chat']['name'],
                        'bot_id' => $item['chat']['bot_id'],
                    ]);
                });

                $user->settings()->create([
                    'country' => $item['settings']['country'] ?? null,
                    'city' => $item['settings']['city'] ?? null,
                    'genre' => $item['settings']['genre'] ?? null,
                    'language_code' => $item['settings']['language_code'] ?? 'en',
                    'events_notifications' => $item['settings']['events_notifications'] ?? false,
                    'events_concerts' => $item['settings']['events_concerts'] ?? false,
                ]);

                $eventsCount = count($item['events']);
                $this->command->info('Processing user: '.$user['id'].' events: '.$eventsCount);
                collect($item['events'])->chunk(1)->each(function ($eventChunk) use ($user, $eventsCount) {
                    foreach ($eventChunk as $event) {
                        $this->command->info('Processing event: '.$event['id']);

                        Event::withoutEvents(function () use ($user, $event) {
                            $newEvent = $user->events()->create([
                                'id' => $event['id'],
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
//
                            $newEvent->status()->create([
                                'user_id' => 1,
                                'status' => $event['confirm'] ? EventStatusEnum::ACCEPTED->value : EventStatusEnum::PENDING->value,
                                'reason' => $event['confirm']['reason'] ?? ($event['delete']['reason'] ?? null),
                                'created_at' => $event['confirm']['created_at'] ?? ($event['delete']['created_at'] ?? now(
                                    )),
                                'updated_at' => $event['confirm']['updated_at'] ?? ($event['delete']['updated_at'] ?? now(
                                    )),
                            ]);
//
                            if (isset($event['media'][0]['original_url'])) {
                                $newEvent->addMediaFromUrl($event['media'][0]['original_url'])
                                    ->toMediaCollection('poster');
                            } else {
                                $this->command->warn('No poster for event: '.$event['id']);
                            }
                        });
                    }
                });
            });
        });
        dd('done');
    }
}
