<?php

namespace App\Telegram;

use DefStudio\Telegraph\Handlers\WebhookHandler;
use Illuminate\Support\Facades\Log;

class TelegraphHandler extends WebhookHandler
{
    public function start(): void
    {
        Log::info('✅ telegraph.php was 1111111');
        $this->reply("👋 Welcome to Rocker Bot!");
    }

    public function photo(): void
    {
        $this->reply("📸 A photographer will join your concert!");
    }
}
