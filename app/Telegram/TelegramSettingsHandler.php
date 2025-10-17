<?php

namespace App\Telegram;

use DefStudio\Telegraph\Keyboard\Button;
use Illuminate\Support\Facades\Log;


trait TelegramSettingsHandler
{
/// Settings
    public function settings(): void
    {
        $buttons = [
            Button::make(trans('settings.country'))->action('get_countries'),
            Button::make(trans('settings.city'))->action('get_cities'),
            Button::make('back')->action('menu'),
        ];

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(
            trans('menu.menu'),
            $buttons,
            $lastMessageId
        );
    }


    public function get_countries(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];
        Log::info('countries');
        foreach (trans('settings.countries') as $icon => $value) {
            Log::info('auth37', [auth()->user()]);
            $checked = auth()->user()?->settings?->country === $value ? ' ☑️' : '';
//            $checked = auth()->user()?->load(['settings', 'chat'])->settings?->country === $value ? ' ☑️' : '';

            Log::info('78744');

            array_unshift(
                $buttons,
                Button::make($icon.$checked)->action('update_settings')
                    ->param('key', 'country')->param('value', $value)
            );
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_country'), $buttons, $lastMessageId);
    }


    public function get_cities($country = null): void
    {
        $country = $country ?? auth()->user()->settings->country;

//        if (!$country) {
//            $this->get_countries();
//        }

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

    public function update_settings($key, $value): void
    {
        auth()->user()->settings()->updateOrCreate([], [$key => $value]);

        if ($key === 'country') {
            if ($value === 'all') {
                auth()->user()->settings()->update(['city' => 'all']);
                $this->settings();
                $this->reply("Saved");
            } else {
                $this->get_cities($value);
            }
        } elseif ($key === 'city') {
            $this->reply("Saved");
            $this->menu();
            sleep(2);
            $this->chat->message(trans('messages.change_city'))->send();
        }
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
