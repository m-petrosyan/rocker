<?php

namespace App\Console\Commands;

use App\Models\UserBot;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Log;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        try {
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
            $eventsAdded = $this->addEventsToSitemap($sitemap);

            if (!$eventsAdded) {
                $this->warn('Sitemap generation skipped due to API failure.');
                $this->notifyTelegram("⚠️ Sitemap generation skipped due to API failure.");
            } else {
                // Write sitemap only if events successfully fetched
                $sitemap->writeToFile(public_path('sitemap.xml'));
                $this->info('Sitemap generated successfully!');
            }

        } catch (\Throwable $e) {
            Log::error('Sitemap cron fatal error: '.$e->getMessage());
            $this->notifyTelegram("⚠️ Sitemap fatal error: ".$e->getMessage());
        }
    }

    private function addEventsToSitemap(Sitemap $sitemap): bool
    {
        $this->info('Fetching all events from API...');
        $params = [
            'limit' => 50000,
            'page' => 1,
            'past' => true,
        ];

        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                $this->info("Making API request".($retryCount > 0 ? " (retry {$retryCount})" : ""));

                $response = Http::timeout(60)
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
                    return false;
                }

                $totalEvents = 0;
                foreach ($data['data'] as $event) {
                    $eventUrl = config('app.url').'/events/'.$event['id'];
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

                return true;
            } catch (RequestException $e) {
                Log::error("HTTP error while fetching events: ".$e->getMessage());
                $retryCount++;
                sleep(5);
            } catch (\Throwable $e) {
                Log::error("Error fetching events from API: ".$e->getMessage());
                $retryCount++;
                sleep(5);
            }
        }

        $this->error("Failed to fetch events after {$maxRetries} attempts. Sitemap creation skipped.");
        $this->notifyTelegram("⚠️ Sitemaps error, failed to fetch events after {$maxRetries} attempts");

        return false;
    }

    /**
     * Notify Telegram if chat exists, safely.
     */
    private function notifyTelegram(string $message): void
    {
        try {
            $chat = UserBot::where('chat_id', config('telegraph.webhook.chat_id'))->first();
            if ($chat) {
                $chat->message($message)->send();
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send Telegram message: ".$e->getMessage());
        }
    }
}
