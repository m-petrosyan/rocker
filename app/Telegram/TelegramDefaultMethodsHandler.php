<?php

namespace App\Telegram;

use App\Models\UserBot;
use DefStudio\Telegraph\DTO\User;
use Illuminate\Support\Facades\Log;

trait TelegramDefaultMethodsHandler
{
    protected function handleChatMemberJoined(UserBot|User $member): void
    {
        Log::info('handleChatMemberJoined');
        $this->chat->html("Welcome")->send();
    }

    protected function handleMigrateToChat(): void
    {
        Log::info('handleMigrateToChat');
        $this->chat->html("Chat migrated")->send();
    }


}
