<?php

namespace App\Console\Commands;

use App\Models\UserBot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';

    protected $description = 'Generate the sitemap for the application';

    protected const DROP_THRESHOLD_PERCENT = 50;

    public function handle(): void
    {
        app()->instance('sitemap_mode', true);

        try {
            $previousCount = $this->getPreviousUrlCount();

            $this->info('Generating sitemap...');

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

            $sitemap->writeToFile(public_path('sitemap.xml'));

            $newCount = count($sitemap->getTags());

            $this->info("✅ Sitemap generated successfully! URLs: {$newCount}");

            $this->checkForSharpDrop($previousCount, $newCount);
        } catch (\Throwable $e) {
            $errorMessage = '⚠️ Sitemap generation failed: '.$e->getMessage();

            Log::error($errorMessage);
            $this->error($errorMessage);

            $this->notifyTelegram($errorMessage);
        }
    }

    private function getPreviousUrlCount(): ?int
    {
        $path = public_path('sitemap.xml');

        if (! file_exists($path)) {
            return null;
        }

        try {
            $xml = new SimpleXMLElement(file_get_contents($path));

            $namespaces = $xml->getNamespaces(true);
            $ns = '';

            if (isset($namespaces[''])) {
                $ns = $namespaces[''];
            }

            if ($ns) {
                $xml->registerXPathNamespace('s', $ns);
                $urls = $xml->xpath('//s:url');
            } else {
                $urls = $xml->xpath('//url');
            }

            return $urls ? count($urls) : 0;
        } catch (\Throwable $e) {
            $this->warn('Could not parse previous sitemap: '.$e->getMessage());

            return null;
        }
    }

    private function checkForSharpDrop(?int $previousCount, int $newCount): void
    {
        if ($previousCount === null) {
            $this->info('No previous sitemap to compare against.');

            return;
        }

        if ($previousCount === 0) {
            $this->info('Previous sitemap was empty, skipping drop detection.');

            return;
        }

        $dropPercent = (1 - $newCount / $previousCount) * 100;

        if ($dropPercent >= self::DROP_THRESHOLD_PERCENT) {
            $message = sprintf(
                '⚠️ Sitemap URL count dropped sharply! Previous: %d, New: %d (%.1f%% decrease)',
                $previousCount,
                $newCount,
                $dropPercent,
            );

            $this->warn($message);
            Log::warning($message);

            $this->notifyTelegram($message);
        } else {
            $this->info(sprintf(
                'URL count change: %d → %d (%.1f%% decrease). Within threshold.',
                $previousCount,
                $newCount,
                $dropPercent,
            ));
        }
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
            Log::error('Failed to send Telegram message: '.$e->getMessage());
        }
    }
}
