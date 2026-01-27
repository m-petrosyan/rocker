<?php

namespace App\Console\Commands;

use App\Models\UserBot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command
{
    protected $signature = 'app:sitemap';
    protected $description = 'Generate the sitemap for the application';

    public function handle(): void
    {
        app()->instance('sitemap_mode', true);

        try {
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

            $this->info('✅ Sitemap generated successfully!');
        } catch (\Throwable $e) {
            $errorMessage = '⚠️ Sitemap generation failed: '.$e->getMessage();

            Log::error($errorMessage);
            $this->error($errorMessage);

            $this->notifyTelegram($errorMessage);
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
            Log::error("Failed to send Telegram message: ".$e->getMessage());
        }
    }
}
