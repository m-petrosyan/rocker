<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        \Log::info('Sitemap generation started.', ['time' => now()]);

        // Создаём объект Sitemap
        $sitemap = Sitemap::create();

        // Обход сайта с помощью SitemapGenerator
        SitemapGenerator::create(config('app.url'))
            ->setConcurrency(10)
            ->hasCrawled(function (Url $url) use ($sitemap) {
                $exclude = ['login', 'register', 'forgot-password', 'profile'];
                $firstSegment = $url->segment(1) ?? '';
                if (in_array($firstSegment, $exclude)) {
                    return null;
                }
                $sitemap->add($url);

                return $url;
            });

        // Получаем все события из API
        try {
            $params = [
                'limit' => 100, // Уменьшаем limit для оптимизации
                'page' => 1,
                // Убрали 'past' => true, чтобы получить все события
            ];

            $totalEvents = 0;
            do {
                $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
                if ($response->successful()) {
                    $responseData = $response->json();
                    $events = $responseData['data'] ?? [];
                    foreach ($events as $event) {
                        if (isset($event['id'])) {
                            $sitemap->add(Url::create("https://rocker.am/events/{$event['id']}"));
                            $totalEvents++;
                        }
                    }
                    \Log::info('Events added from API.', [
                        'page' => $params['page'],
                        'count' => count($events),
                        'total' => $totalEvents,
                    ]);
                    $params['page']++;
                    $nextPageUrl = $responseData['links']['next'] ?? null;
                } else {
                    \Log::error('Failed to fetch events from API.', ['status' => $response->status()]);
                    break;
                }
            } while ($nextPageUrl);
        } catch (\Exception $e) {
            \Log::error('Error fetching events from API.', ['error' => $e->getMessage()]);
        }

        // Сохраняем sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));
        \Log::info('Sitemap generation completed.', ['time' => now(), 'total_events' => $totalEvents]);
    }
}
