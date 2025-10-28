<?php

namespace App\Observers;

use App\Models\Bot;
use App\Models\User;
use App\Models\UserBot;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserBotObserver
{
    public function created(UserBot $userBot): void
    {
        $maxAttempts = 2;
        $attempt = 1;

        while ($attempt <= $maxAttempts) {
            $data = $userBot->memberInfo($userBot->chat_id)->user()->toArray();


            $data['name'] = $data['first_name'].' '.$data['last_name'];
//            Log::info('data', [$data]);
            if (empty($data['username']) || User::where('username', $data['username'])->exists()) {
                $data['username'] = $data['username'].'_bot_'.strtolower(uniqid());
            }

            try {
                $user = User::query()->create(Arr::only($data, ['username', 'name']))->assignRole('user');
                Log::info('create user', ['user_id' => $user->id, 'attempt' => $attempt]);
            } catch (\Exception $e) {
                Log::error(
                    'Ошибка создания пользователя',
                    ['error' => $e->getMessage(), 'data' => $data, 'attempt' => $attempt]
                );
                $user = null;
            }

            if ($user) {
                if ($userBot->update(['user_id' => $user->id])) {
                    $user->settings()->create(
                        ['language_code' => $data['language_code'], 'genre' => 'all', 'city' => 'all']
                    );
                } else {
                    Log::error('Ошибка обновления userBot', ['user_id' => $user->id, 'attempt' => $attempt]);
                }
                break;
            } else {
                if ($attempt === $maxAttempts) {
                    Log::info(
                        'Удаление userBot из-за неудачного создания пользователя',
                        ['maxAttempts' => $maxAttempts, 'attempt' => $attempt]
                    );

                    $userBot->delete();
                    Bot::sendMessage([
                        'chat_id' => $userBot->chat_id,
                        'text' => 'Не удалось создать пользователя после нескольких попыток. Ваш бот будет удалён.',
                    ]);
                }
            }

            $attempt++;

            Log::info('Попытка создания пользователя', ['attempt' => $attempt]);
        }
    }
}
