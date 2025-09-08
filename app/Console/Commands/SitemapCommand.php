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

        try {
            // 1. Генерируем sitemap через краулер
            $generated = SitemapGenerator::create(config('app.url'))
                ->setConcurrency(10)
                ->hasCrawled(function (Url $url) {
                    $exclude = ['login', 'register', 'forgot-password', 'profile'];
                    $firstSegment = $url->segment(1) ?? '';
                    if (in_array($firstSegment, $exclude)) {
                        return null;
                    }

                    return $url;
                })
                ->getSitemap();

            // 2. Создаём итоговый sitemap и добавляем туда все найденные ссылки
            $sitemap = Sitemap::create();
            foreach ($generated->getTags() as $tag) {
                $sitemap->add($tag);
            }

            // 3. Добавляем события из API
            $params = [
                'limit' => 10000,
                'page' => 1,
                'past' => true,
            ];

            do {
                $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
                if ($response->successful()) {
                    $responseData = $response->json();
                    $events = $responseData['data'] ?? [];

                    foreach ($events as $event) {
                        if (isset($event['id'])) {
                            $sitemap->add(Url::create("/events/{$event['id']}"));
                        }
                    }

                    \Log::info('Events added from API.', [
                        'page' => $params['page'],
                        'count' => count($events),
                    ]);

                    $params['page']++;
                    $nextPageUrl = $responseData['links']['next'] ?? null;
                } else {
                    \Log::error('Failed to fetch events from API.', [
                        'status' => $response->status(),
                    ]);
                    break;
                }
            } while ($nextPageUrl);

            // 4. Сохраняем итоговый sitemap
            $sitemap->writeToFile(public_path('sitemap.xml'));
            \Log::info('Sitemap generation completed.', ['time' => now()]);
        } catch (\Exception $e) {
            \Log::error('Sitemap generation failed.', ['error' => $e->getMessage()]);
        }
    }
}
