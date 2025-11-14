<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class FacebookScrapperController
{
    /**
     * Получить все события со страницы Facebook
     *
     * GET /api/facebook-events?url=https://www.facebook.com/ZheshtEvents
     */
    public function getEvents(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $pageUrl = $request->query('url');

        try {
            // Получаем список событий
            $events = $this->scrapeEventsList($pageUrl);

            // Для каждого события получаем детали
            $detailedEvents = [];
            foreach ($events as $event) {
                $details = $this->scrapeEventDetails($event['url']);
                $detailedEvents[] = array_merge($event, $details);

                // Небольшая задержка между запросами
                sleep(1);
            }

            return response()->json([
                'success' => true,
                'count' => count($detailedEvents),
                'events' => $detailedEvents,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Получить список событий со страницы
     */
    private function scrapeEventsList(string $pageUrl): array
    {
        $html = Browsershot::url($pageUrl)
            ->setChromePath('/usr/bin/google-chrome-stable')
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->timeout(60)
            ->bodyHtml();

        $crawler = new Crawler($html);
        $events = [];

        // Ищем все ссылки на события
        $crawler->filter('a[href*="/events/"]')->each(function (Crawler $node) use (&$events) {
            $href = $node->attr('href');

            if (preg_match('/\/events\/(\d+)/', $href, $matches)) {
                $eventId = $matches[1];
                $title = trim($node->text());

                if (!empty($title) && strlen($title) > 3 && !isset($events[$eventId])) {
                    $events[$eventId] = [
                        'id' => $eventId,
                        'title' => $title,
                        'url' => 'https://www.facebook.com/events/'.$eventId,
                    ];
                }
            }
        });

        return array_values($events);
    }

    /**
     * Получить детали события
     */
    private function scrapeEventDetails(string $eventUrl): array
    {
        $html = Browsershot::url($eventUrl)
            ->setChromePath('/usr/bin/google-chrome-stable')
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->timeout(60)
            ->bodyHtml();

        $crawler = new Crawler($html);

        $data = [
            'title' => '',
            'description' => '',
            'start_date' => '',
            'start_time' => '',
            'location' => '',
            'image' => '',
            'price' => '',
            'ticket_url' => '',
        ];

        // Название
        try {
            $titleNode = $crawler->filter('h1')->first();
            if ($titleNode->count() > 0) {
                $data['title'] = trim($titleNode->text());
            }
        } catch (\Exception $e) {
        }

        // Изображение - OG или fallback
        try {
            $ogImage = $crawler->filter('meta[property="og:image"]')->attr('content');
            if (!empty($ogImage)) {
                $data['image'] = $ogImage;
            } else {
                $images = $crawler->filter('img')->each(function (Crawler $node) {
                    return [
                        'src' => $node->attr('src'),
                        'width' => (int)($node->attr('width') ?: 0),
                        'height' => (int)($node->attr('height') ?: 0),
                    ];
                });

                $images = array_filter($images, function ($img) {
                    return !empty($img['src']) &&
                        !str_contains($img['src'], 'data:image') &&
                        ($img['width'] > 200 || $img['height'] > 200) &&
                        !str_contains($img['src'], 'profile') &&
                        !str_contains($img['src'], 'logo');
                });

                usort($images, function ($a, $b) {
                    $sizeA = $a['width'] * $a['height'];
                    $sizeB = $b['width'] * $b['height'];

                    return $sizeB - $sizeA;
                });

                if (!empty($images)) {
                    $data['image'] = $images[0]['src'];
                }
            }
        } catch (\Exception $e) {
        }

        // Описание - OG или длинный контент
        try {
            $ogDesc = $crawler->filter('meta[property="og:description"]')->attr('content');
            if (!empty($ogDesc) && strlen($ogDesc) > 50) {
                $data['description'] = trim($ogDesc);
            } else {
                $allText = $this->getFilteredText($crawler);
                $descriptions = array_filter($allText, function ($text) {
                    return strlen($text) > 100 && strlen($text) < 2000 &&
                        !preg_match('/^\d+$/', $text) &&
                        str_word_count($text) > 10 &&
                        !str_contains(strtolower($text), 'log in') &&
                        !str_contains(strtolower($text), 'facebook');
                });

                if (!empty($descriptions)) {
                    $data['description'] = reset($descriptions);
                }
            }
        } catch (\Exception $e) {
        }

        // Получаем отфильтрованный текст
        $allText = $this->getFilteredText($crawler);

        // Полный текст main для парсинга даты (без разбиения)
        $mainText = '';
        try {
            $mainText = $crawler->filter('div[role="main"]')->text();
        } catch (\Exception $e) {
            $mainText = $crawler->filter('body')->text();
        }
        $normalizedMain = $this->normalizeSpaces($mainText);

        // Start_date и start_time - полный формат с "at" в полном тексте
        // Start_date и start_time - полный формат с "at" в полном тексте
        $dateTimeFound = false;
        $dayNames = '(Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)';
        if (preg_match(
            "/{$dayNames},\s+\w+\s+\d{1,2}(?:st|nd|rd|th)?,\s+\d{4}\s+at\s+\d{1,2}(?::?\d{2})?\s*(?:AM|PM)?/i",
            $normalizedMain,
            $match
        )) {
            try {
                $fullDateTime = preg_replace(
                    '/\s+(?:st|nd|rd|th),?\s*/i',
                    ' ',
                    trim($this->normalizeSpaces($match[0]))
                );
                // Убираем возможные ведущие цифры перед днем недели
                $fullDateTime = preg_replace('/^\d+\s*/', '', $fullDateTime);
                // Точный формат
                $format = 'l, F j, Y \a t g:i A';
                if (!strpos($fullDateTime, ':')) {
                    $format = 'l, F j, Y \a t g A';
                }
                $parsed = Carbon::createFromFormat($format, $fullDateTime);
                if (!$parsed) {
                    throw new \Exception('Format failed');
                }
                if ($parsed->isFuture()) {
                    $data['start_date'] = $parsed->format('Y-m-d');
                    $data['start_time'] = $parsed->format('H:i');
                    $dateTimeFound = true;
                }
            } catch (\Exception $e) {
                // Fallback к parse с ручной очисткой
                try {
                    $cleanDateTime = preg_replace('/^\d+\s*/', '', $fullDateTime ?? $match[0]);
                    $parsed = Carbon::parse($cleanDateTime);
                    if ($parsed->isFuture()) {
                        $data['start_date'] = $parsed->format('Y-m-d');
                        $data['start_time'] = $parsed->format('H:i');
                        $dateTimeFound = true;
                    }
                } catch (\Exception $e2) {
                    // Пропускаем
                }
            }
        }

        // Fallback для даты - поиск всех паттернов в полном тексте
        if (empty($data['start_date'])) {
            $potentialDates = [];
            $datePatterns = [
                '/\w+,\s+\w+\s+\d{1,2}(?:st|nd|rd|th)?,\s+\d{4}/i',
                '/\d{1,2}(?:st|nd|rd|th)?\s+\w+\s+\d{4}/i',
                '/\w+\s+\d{1,2}(?:st|nd|rd|th)?,\s+\d{4}/i',
                '/\d{1,2}\/\d{1,2}\/\d{4}/',
                '/\d{4}-\d{2}-\d{2}/',
            ];
            foreach ($datePatterns as $pattern) {
                preg_match_all($pattern, $normalizedMain, $matches);
                foreach ($matches[0] as $m) {
                    try {
                        $parsedDate = Carbon::parse($this->normalizeSpaces($m));
                        if ($parsedDate->isFuture()) {
                            $potentialDates[] = $parsedDate;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
            if (!empty($potentialDates)) {
                $earliest = collect($potentialDates)->sortBy('timestamp')->first();
                $data['start_date'] = $earliest->format('Y-m-d');
            }
        }

        // Fallback для времени
        if (empty($data['start_time'])) {
            foreach ($allText as $text) {
                $normalized = $this->normalizeSpaces($text);
                if (preg_match('/\d{1,2}(?::?\d{2})?\s*(?:AM|PM)?/i', $normalized, $match)) {
                    $timeStr = trim($match[0]);
                    // Нормализуем: добавляем :00 если нет минут, и AM/PM если есть
                    if (!preg_match('/:/', $timeStr)) {
                        $parts = preg_split('/\s+/', $timeStr);
                        $hour = $parts[0];
                        $ampm = isset($parts[1]) ? strtoupper($parts[1]) : '';
                        $timeStr = $hour.':00'.($ampm ? ' '.$ampm : '');
                    }
                    try {
                        $timeCarbon = Carbon::parse($timeStr);
                        $data['start_time'] = $timeCarbon->format('H:i');
                    } catch (\Exception $e) {
                        $data['start_time'] = $timeStr; // Fallback
                    }
                    break;
                }
            }
        }

        // Место - улучшенный поиск с фильтрацией
        foreach ($allText as $index => $text) {
            if (empty($data['location'])) {
                $lowerText = strtolower($text);
                if ((stripos($text, 'Location') !== false ||
                        stripos($text, 'Venue') !== false ||
                        stripos($text, 'Where') !== false ||
                        stripos($text, 'Address') !== false ||
                        stripos($text, 'at ') !== false) &&
                    !str_contains($lowerText, 'log in') &&
                    !str_contains($lowerText, 'facebook') &&
                    !str_contains($lowerText, 'forgot')) {
                    if (isset($allText[$index + 1]) && strlen($allText[$index + 1]) > 5) {
                        $candidate = trim($allText[$index + 1]);
                        if (strlen($candidate) > 10 && !str_contains(strtolower($candidate), 'log in')) {
                            $data['location'] = $candidate;
                            break;
                        }
                    } elseif (strlen($text) > 10) {
                        $data['location'] = trim(preg_replace('/^(Location|Venue|Where|Address):\s*/i', '', $text));
                        break;
                    }
                }
            }
        }

        // Паттерны адресов
        if (empty($data['location'])) {
            foreach ($allText as $text) {
                $lowerText = strtolower($text);
                if ((!str_contains($lowerText, 'log in') &&
                        !str_contains($lowerText, 'facebook') &&
                        !str_contains($lowerText, 'forgot')) &&
                    (preg_match(
                            '/(?:\d+\s+(?:Street|St\.?|Avenue|Ave\.?|Road|Rd\.?|Boulevard|Blvd\.?)|at\s+[A-Z][a-z]+.*)/i',
                            $text
                        ) ||
                        preg_match('/^[A-Z][a-z]+(?:\s+[A-Z][a-z]+)*,\s*[A-Z][a-z]+/u', $text))) {
                    $data['location'] = trim($text);
                    break;
                }
            }
        }

        // Цена - разные форматы
        foreach ($allText as $text) {
            if (empty($data['price'])) {
                if (preg_match('/\$\d+(?:\.\d{2})?/', $text, $match)) {
                    $data['price'] = $match[0];
                    continue;
                }
                if (preg_match('/\d+\s*AMD/i', $text, $match)) {
                    $data['price'] = $match[0];
                    continue;
                }
                if (preg_match('/\d+\s*֏/', $text, $match)) {
                    $data['price'] = $match[0];
                    continue;
                }
                if (preg_match('/\b(?:Free|Անվճար)\b/i', $text, $match)) {
                    $data['price'] = 'Free';
                    continue;
                }
            }
        }

        // Ссылка на билеты - с парсингом редиректа FB
        try {
            $found = false;
            $crawler->filter('a')->each(function (Crawler $node) use (&$data, &$found) {
                if ($found) {
                    return;
                }

                $href = $node->attr('href') ?: '';
                $text = strtolower(trim($node->text()));

                // Парсинг FB редиректа
                if (preg_match('/l\.facebook\.com\/l\.php\?u=([^&]+)/', $href, $matches)) {
                    $redirectUrl = urldecode($matches[1]);
                    $data['ticket_url'] = $redirectUrl;
                    $found = true;

                    return;
                }

                $ticketKeywords = ['ticket', 'buy', 'билет', 'գնել', 'purchase', 'register', 'get tickets', 'buy now'];
                $ticketDomains = ['ticket', 'eventbrite', 'ticketmaster', 'iticket', 'tkt'];

                foreach ($ticketKeywords as $keyword) {
                    if (stripos($text, $keyword) !== false) {
                        $data['ticket_url'] = $href;
                        $found = true;

                        return;
                    }
                }

                foreach ($ticketDomains as $domain) {
                    if (stripos($href, $domain) !== false) {
                        $data['ticket_url'] = $href;
                        $found = true;

                        return;
                    }
                }
            });
        } catch (\Exception $e) {
        }

        return $data;
    }

    /**
     * Получить отфильтрованный текст, без UI-мусора
     */
    private function getFilteredText(Crawler $crawler): array
    {
        $allText = $crawler->filter('span, div, p')->each(function (Crawler $node) {
            $text = trim($node->text());

            return $this->normalizeSpaces($text);
        });

        $allText = array_filter($allText, function ($text) {
            $lower = strtolower($text);

            return !empty($text) &&
                strlen($text) > 1 &&
                !str_contains($lower, 'log in') &&
                !str_contains($lower, 'forgot account') &&
                !str_contains($lower, 'facebook') &&
                !str_contains($lower, 'privacy') &&
                !str_contains($lower, 'terms');
        });

        return array_values(array_unique($allText));
    }

    /**
     * Нормализовать пробелы (nbsp, narrow space → обычный)
     */
    private function normalizeSpaces(string $text): string
    {
        return preg_replace('/\s+/', ' ', str_replace(["\xc2\xa0", "\u{202F}", "\u{00A0}"], ' ', $text));
    }
}