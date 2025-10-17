<?php

use App\Models\UserBot;

$userId = data_get(request()->input('callback_query'), 'from.id') ?? data_get(request()->input('message'), 'from.id');
\Illuminate\Support\Facades\Log::info('auting878');
if ($userId) {
    $userBot = UserBot::query()->where('chat_id', $userId)->first();
    \Illuminate\Support\Facades\Log::info('auting878');
    if ($userBot) {
        \Illuminate\Support\Facades\Log::info('auting879');
        auth()->loginUsingId($userBot->user?->id);
    }
}
