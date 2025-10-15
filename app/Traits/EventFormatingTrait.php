<?php

namespace App\Traits;

use App\Enums\EventGenreEnum;
use App\Enums\EventTypeEnum;
use DefStudio\Telegraph\Keyboard\Button;


trait EventFormatingTrait
{
    public function getEventContent(object $event): string
    {
        $eventData = [
            'title' => sprintf("<b>%s</b>\n\n", $event->title),
            'location' => sprintf("📍 %s %s\n\n", $event->location, $this->getCountryFlag($event->country)),
        ];

        if ($event->start_date) {
            $eventData['start_date'] = sprintf(
                "⏰ %s\n\n",
                $this->formatDateTime($event->start_date_short, $event->start_time)
            );
        }

        if ($event->genre) {
            $eventData['genre'] = sprintf("%s\n\n", $this->formatGenre($event->genre));
        }

        $eventData['type'] = sprintf("%s\n\n", $this->formatEventType($event->type));

        if ($event->price) {
            $eventData['price'] = sprintf("💵 %s\n\n", $event->price);
        }

        $eventData['content'] = $this->appendBotSignature($event->content);

        return implode('', $eventData);
    }

    private function getCountryFlag(string $countryCode): string
    {
        return $countryCode === 'am' ? '🇦🇲' : '🇬🇪';
    }

    private function formatDateTime(string $date, ?string $time): string
    {
        return trim($date.($time ? ' '.$time : ''));
    }

    private function formatGenre(string $genre): string
    {
        $genres = [
            EventGenreEnum::ROCK->value => trans('genres.rock'),
            EventGenreEnum::METAL->value => trans('genres.metal'),
        ];

        if (isset($genres[$genre])) {
            return $genres[$genre];
        }

        return sprintf('%s / %s', trans('genres.rock'), trans('genres.metal'));
    }

    private function formatEventType(string $type): string
    {
        return $type === EventTypeEnum::CONCERT->value ? '🎙️ concert' : '🎉 event';
    }

    private function appendBotSignature(string $content): string
    {
        $maxContentLength = 800;
        $botSignature = "\n\n🎉 Rock/Metal Events Bot Calendar in Armenia/Georgia: t.me/RockMetalEventsbot";

        return mb_strlen($content, 'UTF-8') >= $maxContentLength ? $content : $content.$botSignature;
    }

    public function getButtons(object $event): array
    {
        $buttons[] = Button::make('Add to favorites')
            ->action('add_to_favorite')
            ->param('eventId', $event->id);

        if (!empty($event->link)) {
            $buttons[] = Button::make('Event link')->url($event->link);
        }

        if (!empty($event->ticket)) {
            $buttons[] = Button::make('Tickets')->url($event->ticket);
        }

        if ($event->country === 'am') {
            $buttons[] = Button::make('Rocker link')
                ->url(route('events.show', $event->id));
        }

        return $buttons;
    }
}
