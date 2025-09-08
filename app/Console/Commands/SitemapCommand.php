<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
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
                    \Log::info('API response:', ['response' => $responseData]);
                    if (!isset($responseData['data']) || !is_array($responseData['data'])) {
                        \Log::error('Invalid API response structure');
                        break;
                    }
                    $events = $responseData['data'];
                    foreach ($events as $event) {
                        if (isset($event['id'])) {
                            $generated->add(Url::create("/events/{$event['id']}"));
                        }
                    }
                    $params['page']++;
                    $nextPageUrl = $responseData['links']['next'] ?? null;
                } elseif ($response->status() === 429) {
                    $retryAfter = $response->header('Retry-After') ?? 60;
                    \Log::warning("API rate limit exceeded, waiting $retryAfter seconds...");
                    sleep($retryAfter);
                    continue;
                } else {
                    \Log::error('API request failed', ['status' => $response->status()]);
                    break;
                }
            } while ($nextPageUrl);
        } catch (\Exception $e) {
            \Log::error('Error fetching events from API.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        \Log::info('Attempting to write sitemap to file');
        $generated->writeToFile(public_path('sitemap.xml'));
        \Log::info('Sitemap written to file', ['urls' => array_map(fn($tag) => $tag->url, $generated->getTags())]);
    }
}
