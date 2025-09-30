<?php

use App\Models\UserBot;

$userId = data_get(request()->input('callback_query'), 'from.id') ?? data_get(request()->input('message'), 'from.id');

if ($userId) {
    $userBot = UserBot::query()->where('chat_id', $userId)->first();

    if ($userBot) {
        auth()->loginUsingId($userBot->user?->id);
    }
}
