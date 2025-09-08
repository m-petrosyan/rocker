<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        \Log::info('Sitemap generation started.', ['time' => now()]);

        // Сначала генерируем базовый sitemap
        SitemapGenerator::create(config('app.url'))
            ->setConcurrency(10)
            ->hasCrawled(function (Url $url) {
                $exclude = ['login', 'register', 'forgot-password', 'profile'];
                $firstSegment = $url->segment(1) ?? '';

                if (in_array($firstSegment, $exclude)) {
                    return null;
                }

                return $url;
            })
            ->writeToFile(public_path('sitemap-temp.xml'));

        // Загружаем уже сгенерированные URL
        $sitemap = Sitemap::createFromFile(public_path('sitemap-temp.xml'));

        // Добавляем все события
        $allEvents = Event::all();
        foreach ($allEvents as $event) {
            $sitemap->add(
                Url::create("/events/{$event->id}")
                    ->setLastModificationDate($event->updated_at)
            );
        }

        // Добавляем страницы пагинации для past events
        $perPage = 52;
        $totalPastPages = ceil(Event::where('start_date', '<', now())->count() / $perPage);
        for ($i = 1; $i <= $totalPastPages; $i++) {
            $sitemap->add(Url::create("/events/past?page={$i}"));
        }

        // Записываем финальный sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        // Удаляем временный файл
        if (file_exists(public_path('sitemap-temp.xml'))) {
            unlink(public_path('sitemap-temp.xml'));
        }

        \Log::info('Sitemap generation finished.', ['time' => now()]);
    }
}
