<?php

use App\Models\UserBot;

if (data_get(request()->input('message'), 'from.id')) {
    $userBot = UserBot::query()->where('chat_id', data_get(request()->input('message'), 'from.id'))->first();
    if ($userBot) {
        auth()->loginUsingId($userBot->user?->id);
    }
}
//Log::info(1, [data_get(request()->input('message'), 'from.id')]);
//Log::info(2, [$this->chat->user]);

