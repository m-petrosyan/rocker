<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        \Log::info('Sitemap generation started.', ['time' => now()]);

        $sitemap = Sitemap::create();

        // Добавляем статические страницы
        $staticUrls = [
            '/',
            '/events',
            '/bands',
            '/galleries',
        ];

        foreach ($staticUrls as $url) {
            $sitemap->add(Url::create($url));
        }

        // Добавляем все events
        Event::chunk(100, function ($events) use ($sitemap) {
            foreach ($events as $event) {
                $sitemap->add(
                    Url::create("/events/{$event->id}")
                        ->setLastModificationDate($event->updated_at)
                );
            }
        });

        // Добавляем страницы пагинации past events
        $perPage = 52;
        $totalPastPages = ceil(Event::where('start_date', '<', now())->count() / $perPage);
        for ($i = 1; $i <= $totalPastPages; $i++) {
            $sitemap->add(Url::create("/events/past?page={$i}"));
        }

        // Записываем sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        \Log::info('Sitemap generation finished.', ['time' => now()]);
    }
}
