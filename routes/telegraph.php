<?php

use App\Models\UserBot;

\Illuminate\Support\Facades\Log::info('78787878asdasdasd');

$userId = data_get(request()->input('callback_query'), 'from.id') ?? data_get(request()->input('message'), 'from.id');
\Illuminate\Support\Facades\Log::info('$userId', ['userId' => $userId]);
if ($userId) {
    $userBot = UserBot::query()->where('chat_id', $userId)->first();
    \Illuminate\Support\Facades\Log::info('$userBot', [$userBot]);
    if ($userBot) {
        \Illuminate\Support\Facades\Log::info('$userBot', [$userBot->user?->id]);
        auth()->loginUsingId($userBot->user?->id);
        \Illuminate\Support\Facades\Log::info('auth()->id()', ['auth_id' => auth()->id()]);
    }
}
