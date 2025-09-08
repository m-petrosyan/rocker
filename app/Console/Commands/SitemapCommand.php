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
        $maxRetries = 3;

        do {
            $params = [
                'limit' => 1000, // Reduced limit to be gentler on the API
                'page' => $page,
                'past' => true,
            ];

            $retryCount = 0;
            $success = false;

            while ($retryCount < $maxRetries && !$success) {
                try {
                    $this->info("Fetching page {$page}".($retryCount > 0 ? " (retry {$retryCount})" : ""));

                    $response = Http::timeout(30)
                        ->retry(3, 2000) // Retry 3 times with 2 second delay
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
                        $success = true;
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
                    $success = true;

                    // Add delay between requests to be respectful
                    if ($hasNextPage) {
                        $this->info("Waiting 1 second before next request...");
                        sleep(1);
                    }
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    if ($e->response->status() === 429) {
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

            if (!$success) {
                $this->error("Failed to fetch page {$page} after {$maxRetries} retries. Stopping.");
                break;
            }
        } while ($hasNextPage && $page <= ($data['meta']['last_page'] ?? 1));

        $this->info("Total events added to sitemap: {$totalEvents}");
    }
}
