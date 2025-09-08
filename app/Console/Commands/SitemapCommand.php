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

        // Create a new sitemap
        $sitemap = Sitemap::create();

        // Crawl the site for static pages
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
            ->getSitemap(); // Run the crawler to populate $sitemap

        // Fetch events from API
        try {
            $params = [
                'limit' => 10000,
                'page' => 1,
                'past' => false, // Exclude past events to avoid invalid URLs
            ];

            do {
                $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
                if ($response->successful()) {
                    $responseData = $response->json();
                    $events = $responseData['data'] ?? [];
                    foreach ($events as $event) {
                        if (isset($event['id']) && !empty($event['id'])) {
                            // Verify event exists
                            $eventResponse = Http::get("https://bot.rocker.am/api/event/{$event['id']}");
                            if ($eventResponse->successful() && isset($eventResponse->json()['data'])) {
                                $sitemap->add(Url::create("/events/{$event['id']}"));
                            } else {
                                \Log::warning("Skipping event {$event['id']} - not found in API.");
                            }
                        }
                    }
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

        // Write the combined sitemap to file
        $sitemap->writeToFile(public_path('sitemap.xml'));
        \Log::info('Sitemap generation completed.', ['time' => now()]);
    }
}
