<?php

namespace App\Traits;

use App\Enums\EventGenreEnum;
use App\Enums\EventTypeEnum;
use Carbon\Carbon;
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

        $isFest = ! empty($event->end_date);
        $eventData['type'] = sprintf("%s\n\n", $this->formatEventType($event->type, $isFest));

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

    private function formatEventType(string $type, bool $isFest = false): string
    {
        if ($isFest) {
            return '🎪 fest';
        }

        return match ($type) {
            EventTypeEnum::CONCERT->value => '🎙️ concert',
            default => '🎉 event',
        };
    }

    private function appendBotSignature(string $content): string
    {
        $maxContentLength = 800;
        $botSignature = "\n\n🤘 Rock Events (AM/GE) — t.me/RockMetalEventsbot";

        return mb_strlen($content, 'UTF-8') >= $maxContentLength ? $content : $content.$botSignature;
    }

    public function getButtons(object $event): array
    {
        $buttons[] = Button::make('Add to favorites')
            ->action('add_to_favorite')
            ->param('eventId', $event->id);

        if (! empty($event->link)) {
            $buttons[] = Button::make('Event link')->url($event->link);
        }

        if (! empty($event->ticket)) {
            $buttons[] = Button::make('Tickets')->url($event->ticket);
        }

        if ($event->country === 'am') {
            $buttons[] = Button::make('Rocker link')
                ->url(route('events.show', $event->id));
        }

        $googleUrl = $this->buildGoogleCalendarUrl($event);
        if ($googleUrl) {
            $buttons[] = Button::make('Add to Google Calendar')->url($googleUrl);
        }

        if ($event->cordinates) {
            $buttons[] = Button::make('Get map')->action('get_location')
                ->param('latitude', substr($event->cordinates['latitude'], 0, 10))
                ->param('longitude', substr($event->cordinates['longitude'], 0, 10));
        }

        return $buttons;
    }

    private function buildGoogleCalendarUrl(object $event): ?string
    {
        if (empty($event->start_date)) {
            return null;
        }

        $tz = 'Asia/Yerevan';

        $start = $event->start_date instanceof Carbon
            ? $event->start_date->copy()->setTimezone($tz)
            : Carbon::parse($event->start_date, $tz);

        if ($event->start_time) {
            [$hours, $minutes] = explode(':', $event->start_time);
            $start->setTime((int) $hours, (int) $minutes);
        }

        $end = ! empty($event->end_date)
            ? Carbon::parse($event->end_date, $tz)->endOfDay()
            : (clone $start)->addHours(3);

        $query = http_build_query([
            'action' => 'TEMPLATE',
            'text' => $event->title,
            'details' => strip_tags($event->content ?? ''),
            'location' => $event->location ?? '',
            'dates' => $start->utc()->format('Ymd\THis\Z').'/'.$end->utc()->format('Ymd\THis\Z'),
        ]);

        return 'https://calendar.google.com/calendar/render?'.$query;
    }
}
