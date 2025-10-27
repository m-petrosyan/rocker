<?php

namespace App\Telegram;

use App\Enums\EventGenreEnum;
use App\Enums\GenreEnum;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;


trait TelegramSettingsHandler
{
/// Settings
    public function settings(): void
    {
        $buttons = [
            Button::make(trans('settings.country'))->action('get_countries'),
            Button::make(trans('settings.city'))->action('get_cities'),
            Button::make(trans('settings.genre'))->action('get_genres'),
            Button::make(trans('settings.notify_of_new_events'))->action('get_notifications'),
            Button::make('back')->action('menu'),
        ];

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(
            trans('menu.menu'),
            $buttons,
            $lastMessageId
        );
    }


    public function test()
    {
        $this->chat
            ->html(
                'Smash the floor at the Philharmonia!

Why stay at home on a Saturday when you can let loose with the wildest
#metal bands in town?

Rocking the underground in November are:
▪️ PERFECT LEGACY
▪️ SELARDI
▪️ AZHIROCK - joining us from Iran

Make it loud under one of the oldest buildings in the city!
November 22 (Saturday), 20:00
Armenian State Philharmonia: Ground Floor (2 Abovyan Str)
Tickets:
3000 AMD (early bird)
5000 AMD (doors)

🎉 Rock/Metal Events Bot Calendar in Armenia/Georgia: t.me/RockMetalEventsbot
Why stay at home on a Saturday when you can let loose with the wildest
#metal bands in town?

Rocking the underground in November are:
▪️ PERFECT LEGACY
▪️ SELARDI
▪️ AZHIROCK - joining us from Iran

Make it loud under one of the oldest buildings in the city!
November 22 (Saturday), 20:00
Armenian State Philharmonia: Ground Floor (2 Abovyan Str)
Tickets:
3000 AMD (early bird)
5000 AMD (doors)

Tickets:
3000 AMD (early bird)
5000 AMD (doors)Tickets:
3000 AMD (early bird)
5000 AMD (doors)Tickets:
3000 AMD (early bird)
5000 AMD (doors)
'
            )
            ->keyboard(
                Keyboard::make()->buttons([
                    Button::make('Delete')->action('delete')->param('id', '42'),
                    Button::make('Delete')->action('delete')->param('id', '42'),
                    Button::make('Delete')->action('delete')->param('id', '42'),
                ])
            )
            ->send();

//        $this->chat->message('test message')
//            ->keyboard(
//                Keyboard::make()
//                    ->row([
//                        Button::make('Delete')->action('delete')->param('id', '42'),
//                        Button::make('Dismiss')->action('dismiss')->param('id', '42'),
//                        Button::make('Dismiss')->action('dismiss')->param('id', '42'),
//                    ])
//                    ->row([
//                        Button::make('open')->url('https://test.it'),
//                    ])
//            )
//            ->send();
    }

    public function get_countries(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];
        Log::info('countries1');
        foreach (trans('settings.countries') as $icon => $value) {
            Log::info('auth37', [auth()->user()]);
            $checked = auth()?->user()?->settings?->country === $value ? ' ☑️' : '';

            Log::info('countries2');

            array_unshift(
                $buttons,
                Button::make($icon.$checked)->action('update_settings')
                    ->param('key', 'country')->param('value', $value)
            );
        }
        Log::info('countries3');

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_country'), $buttons, $lastMessageId);
    }


    public function get_genres(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];

        foreach (EventGenreEnum::getValues() as $value) {
            $checked = auth()->user()->settings->genre === $value ? ' ☑️' : '';
            array_unshift(
                $buttons,
                Button::make(trans("genres.$value").$checked)->action('update_settings')
                    ->param('key', 'genre')->param('value', $value)
            );
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_genre'), $buttons, $lastMessageId);
    }


    public function get_cities($country = null): void
    {
        $country = $country ?? auth()->user()->settings->country;

        if (!$country) {
            $this->get_countries();
        }

        $buttons = [
            Button::make('back')->action('settings'),
        ];

        foreach (trans("settings.cities.$country") as $key => $value) {
            $checked = auth()->user()->settings->city === $value ? ' ☑️' : '';

            array_unshift(
                $buttons,
                Button::make($key.$checked)->action('update_settings')
                    ->param('key', 'city')->param('value', $value)
            );
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_city'), $buttons, $lastMessageId);
    }


    public function get_notifications(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];

        $userNotify = auth()->user()->settings->events_notifications;

        foreach ([['text' => '✅ enable', 'value' => 1], ['text' => '❌ disable', 'value' => 0]] as $value) {
            $checked = $userNotify === $value['value'] ? ' ☑️' : '';
            array_unshift(
                $buttons,
                Button::make($value['text'].$checked)->action('update_settings')
                    ->param('key', 'events_notifications')
                    ->param('value', $value['value'])
            );
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.settings'), $buttons, $lastMessageId);
    }

    public function update_settings($key, $value): void
    {
        auth()->user()->settings()->updateOrCreate([], [$key => $value]);

        if ($key === 'country') {
            if ($value === 'all') {
                auth()->user()->settings()->update(['city' => 'all']);
                $this->settings();
            } else {
                $this->get_cities($value);
            }
        } elseif ($key === 'city') {
            $this->menu();
            sleep(2);
            $this->chat->message(trans('messages.change_city'))->send();
        } else {
            auth()->user()->settings()->update([$key => $value]);
            $this->settings();
        }

        $this->reply("Saved");
    }

    public function add_to_favorite(int $eventId): void
    {
        auth()->user()->favorites()->syncWithoutDetaching([$eventId]);

        $this->reply("Added");
    }

    public function remove_from_favorite(int $eventId): void
    {
        auth()->user()->favorites()->detach($eventId);

        $buttons = [
            Button::make('Some text')->action('action')->width(0.66),
            Button::make('Some tex')->action('action')->width(0.33),
        ];


        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());
        $this->sendMessageWithButton(trans('menu.menu'), $buttons, $lastMessageId);

        $this->reply("Removed");
    }
}
