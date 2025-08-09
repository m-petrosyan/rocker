<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserBot;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserBotObserver
{
    public function created(UserBot $userBot): void
    {
        $data = $userBot->memberInfo($userBot->chat_id)->user()->toArray();
        Log::info('ss', $data);
        $data['name'] = $data['first_name'].' '.$data['last_name'];

        $user = User::query()->create(Arr::only($data, [
            'username',
            'name',
        ]))->assignRole('user');

        $userBot->update(['user_id' => $user->id]);

        $user->settings()->create(
            Arr::only($data, ['language_code'])
        );
    }
}
