<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

// Добавляем Sitemap

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        \Log::info('Sitemap generation started.', ['time' => now()]);

        // Создаём объект Sitemap вместо SitemapGenerator для ручного добавления
        $sitemap = Sitemap::create();

        // Обход сайта с помощью SitemapGenerator
        // Crawl сайт и сразу пишем во временный файл
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
            })
            ->writeToFile(public_path('sitemap-temp.xml')); // ⚡ ВАЖНО


        // Получаем события из API
        try {
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
                    \Log::info('Events added from API.', ['page' => $params['page'], 'count' => count($events)]);
                    $params['page']++;
                    $nextPageUrl = $responseData['links']['next'] ?? null;
                } else {
                    \Log::error('Failed to fetch events from API.', ['status' => $response->status()]);
                    break;
                }
            } while ($nextPageUrl);

            // Вручную добавляем /events/17
//            $sitemap->add(Url::create('https://rocker.am/events/17'));

        } catch (\Exception $e) {
            \Log::error('Error fetching events from API.', ['error' => $e->getMessage()]);
        }

        // Сохраняем sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));
        \Log::info('Sitemap generation completed.', ['time' => now()]);
    }
}
