<?php

namespace App\Traits;

use App\Enums\EventGenreEnum;
use App\Enums\EventTypeEnum;


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
                $this->formatDateTime($event->start_date, $event->start_time)
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
}
