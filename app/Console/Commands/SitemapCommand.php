<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        \Log::info('Sitemap generation started.', ['time' => now()]);

        // Создаём sitemap с фильтрацией
        $sitemap = SitemapGenerator::create(config('app.url'))
            ->setConcurrency(10)
            ->hasCrawled(function (Url $url) {
                $exclude = ['login', 'register', 'forgot-password', 'profile'];
                $firstSegment = $url->segment(1) ?? '';
                if (in_array($firstSegment, $exclude)) {
                    return null;
                }

                return $url;
            });

        // Получаем события из API
        try {
            $params = [
                'limit' => 10000,
                'page' => 1,
                'past' => true,
            ];

//            $response = Http::get('https://bot.rocker.am/api/event', $params); // Замените на ваш API
            $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
            if ($response->successful()) {
                $events = $response->json(); // Предполагаем, что API возвращает массив событий
                foreach ($events as $event) {
                    if (isset($event['id'])) {
                        $sitemap->add(Url::create("/events/{$event['id']}"));
                    }
                }
                \Log::info('Events added from API.', ['count' => count($events)]);
            } else {
                \Log::error('Failed to fetch events from API.', ['status' => $response->status()]);
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching events from API.', ['error' => $e->getMessage()]);
        }

        // Сохраняем sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));
        \Log::info('Sitemap generation completed.', ['time' => now()]);
    }
}
