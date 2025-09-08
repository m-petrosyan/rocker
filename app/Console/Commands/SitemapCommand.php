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
        $this->info('Fetching all events from API...');

        $params = [
            'limit' => 50000, // Большой лимит чтобы получить все записи
            'page' => 1,
            'past' => true,
        ];

        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                $this->info("Making API request".($retryCount > 0 ? " (retry {$retryCount})" : ""));

                $response = Http::timeout(60) // Увеличили timeout для большого запроса
                ->get('https://bot.rocker.am/api/event', $params);

                if ($response->status() === 429) {
                    $retryAfter = $response->header('Retry-After', 60);
                    $this->warn("Rate limited. Waiting {$retryAfter} seconds before retry...");
                    sleep((int)$retryAfter);
                    $retryCount++;
                    continue;
                }

                $response->throw();
                $data = $response->json();

                if (empty($data['data'])) {
                    $this->warn('No events found in API response');

                    return;
                }

                $totalEvents = 0;
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

                $this->info("Successfully fetched and processed {$totalEvents} events in 1 API request");
                $this->info("Total records available: ".($data['meta']['total'] ?? 'unknown'));

                return;
            } catch (\Illuminate\Http\Client\RequestException $e) {
                if ($e->response && $e->response->status() === 429) {
                    $retryAfter = $e->response->header('Retry-After', 60);
                    $this->warn("Rate limited. Waiting {$retryAfter} seconds before retry...");
                    sleep((int)$retryAfter);
                    $retryCount++;
                } else {
                    $this->error("HTTP error: ".$e->getMessage());
                    break;
                }
            } catch (\Exception $e) {
                $this->error("Error fetching events from API: ".$e->getMessage());
                $retryCount++;
                if ($retryCount < $maxRetries) {
                    $this->info("Retrying in 5 seconds...");
                    sleep(5);
                }
            }
        }

        $this->error("Failed to fetch events after {$maxRetries} retries");
    }
}
