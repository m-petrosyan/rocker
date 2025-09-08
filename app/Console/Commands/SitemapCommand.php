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
        $this->info('Generating sitemap...');

        // Generate base sitemap
        $sitemap = SitemapGenerator::create(config('app.url'))
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

        // Add events from API
        $this->addEventsToSitemap($sitemap);

        // Write the sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }

    private function addEventsToSitemap(Sitemap $sitemap): void
    {
        $this->info('Fetching events from API...');

        $page = 1;
        $totalEvents = 0;

        do {
            $params = [
                'limit' => 10000,
                'page' => $page,
                'past' => true,
            ];

            try {
                $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
                $data = $response->json();

                if (empty($data['data'])) {
                    break;
                }

                foreach ($data['data'] as $event) {
                    // Add event URL to sitemap
                    // Assuming your event URLs follow pattern: /event/{id}
                    $eventUrl = config('app.url').'/event/'.$event['id'];

                    $sitemap->add(
                        Url::create($eventUrl)
                            ->setLastModificationDate(now())
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.8)
                    );

                    $totalEvents++;
                }

                $this->info("Processed page {$page} - Added ".count($data['data'])." events");

                // Check if we have more pages
                $hasNextPage = !empty($data['links']['next']);
                $page++;
            } catch (\Exception $e) {
                $this->error("Error fetching events from API: ".$e->getMessage());
                break;
            }
        } while ($hasNextPage && $page <= ($data['meta']['last_page'] ?? 1));

        $this->info("Total events added to sitemap: {$totalEvents}");
    }
}
