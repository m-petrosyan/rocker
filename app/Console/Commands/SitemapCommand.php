<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
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

        // Создаём пустой sitemap
        $sitemap = Sitemap::create();

        // Сканируем сайт (базовые страницы)
        SitemapGenerator::create(config('app.url'))
            ->setConcurrency(10)
            ->hasCrawled(function (Url $url) use ($sitemap) {
                $exclude = ['login', 'register', 'forgot-password', 'profile'];
                $firstSegment = $url->segment(1) ?? '';

                if (in_array($firstSegment, $exclude)) {
                    return null;
                }

                // Добавляем URL в наш sitemap
                $sitemap->add($url);

                return null; // предотвращаем дублирование
            })
            ->start(); // Не writeToFile, потому что мы сами будем писать позже

        // Добавляем все события
        $allEvents = Event::all();
        foreach ($allEvents as $event) {
            $sitemap->add(
                Url::create("/events/{$event->id}")
                    ->setLastModificationDate($event->updated_at)
            );
        }

        // Добавляем страницы пагинации past events
        $perPage = 52;
        $totalPastPages = ceil(Event::where('start_date', '<', now())->count() / $perPage);
        for ($i = 1; $i <= $totalPastPages; $i++) {
            $sitemap->add(Url::create("/events/past?page={$i}"));
        }

        // Записываем финальный sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        \Log::info('Sitemap generation finished.', ['time' => now()]);
    }
}
