<?php

namespace App\Services;

use App\Enums\EventTypeEnum;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class FacebookEventImportService
{
    private function cookiesPath(): string
    {
        return storage_path('app/facebook/cookies.json');
    }

    public function loadCookies(): array
    {
        $path = $this->cookiesPath();
        if (! file_exists($path)) {
            return [];
        }
        $raw = file_get_contents($path);
        $cookies = json_decode($raw, true);

        return is_array($cookies) ? $cookies : [];
    }

    public function saveCookies(array $cookies): bool
    {
        $dir = dirname($this->cookiesPath());
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $clean = [];
        foreach ($cookies as $cookie) {
            $clean[] = [
                'name' => $cookie['name'] ?? '',
                'value' => $cookie['value'] ?? '',
                'domain' => $cookie['domain'] ?? '.facebook.com',
                'path' => $cookie['path'] ?? '/',
                'secure' => $cookie['secure'] ?? true,
                'httpOnly' => $cookie['httpOnly'] ?? false,
                'sameSite' => $cookie['sameSite'] ?? 'Lax',
            ];
        }
        $written = file_put_contents($this->cookiesPath(), json_encode($clean, JSON_PRETTY_PRINT));
        Log::info('FacebookEventImport: cookies saved', ['count' => count($clean)]);

        return $written !== false;
    }

    public function extractPageUsername(string $url): ?string
    {
        $url = trim($url);
        $url = strtok($url, '?#') ?: $url;
        $patterns = [
            '#facebook\\.com/([^/]+?)(?:/events)?/?$#i',
            '#fb\\.com/([^/]+?)/?$#i',
            '#facebook\\.com/pages/[^/]+/(\d+)/?#i',
        ];
        $skip = ['events', 'pages', 'groups', 'profile.php', 'me', 'photos', 'about', 'watch', 'reel'];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $username = trim($matches[1]);
                if (! in_array(Str::lower($username), $skip, true)) {
                    return $username;
                }
            }
        }
        if (str_contains($url, 'facebook.com') || str_contains($url, 'fb.com')) {
            $path = parse_url($url, PHP_URL_PATH) ?? '';
            $parts = explode('/', trim($path, '/'));
            $last = end($parts);
            if ($last && ! in_array(Str::lower($last), $skip, true)) {
                return $last;
            }
        }
        if (preg_match('/^[\w.\-]+$/', $url)) {
            return $url;
        }

        return null;
    }

    /**
     * @return array<int, array{name: string, description: string, start_date: string|null, end_date: string|null, start_time: string|null, location: string, image_url: string|null, url: string|null, _image_data?: string|null}>
     */
    public function scrapeEvents(string $username): array
    {
        $cookies = $this->loadCookies();
        $urls = [
            "https://www.facebook.com/{$username}/events/",
            "https://m.facebook.com/{$username}/events/",
            "https://www.facebook.com/{$username}/events_upcoming/",
        ];

        foreach ($urls as $url) {
            try {
                $events = $this->fetchPageWithBrowser($url, $cookies);
                if ($events === null || empty($events)) {
                    continue;
                }
                Log::info('FacebookEventImport: events scraped', [
                    'username' => $username,
                    'url' => $url,
                    'count' => count($events),
                ]);

                return $events;
            } catch (\Throwable $e) {
                Log::debug('FacebookEventImport: url failed', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [];
    }

    /**
     * Import events from a Facebook page URL.
     *
     * @return array{imported: int, skipped: int, errors: int}
     */
    public function importForUrl(string $pageUrl, User $user): array
    {
        $stats = ['imported' => 0, 'skipped' => 0, 'errors' => 0];
        $username = $this->extractPageUsername($pageUrl);
        if (! $username) {
            Log::warning('FacebookEventImport: could not extract username', ['user_id' => $user->id, 'url' => $pageUrl]);
            $stats['errors']++;

            return $stats;
        }
        $events = $this->scrapeEvents($username);
        if (empty($events)) {
            return $stats;
        }
        $country = $user->settings->country ?? config('facebook.default_country', 'am');
        $sourceChatId = 'fb_cookie_'.$username;
        $cookies = $this->loadCookies();

        foreach ($events as $fbEvent) {
            try {
                $sourceMessageId = $fbEvent['id']
                    ? crc32($fbEvent['id'])
                    : crc32($username.$fbEvent['name'].$fbEvent['start_date']);

                $exists = Event::query()
                    ->where('tg_source_chat_id', $sourceChatId)
                    ->where('tg_source_message_id', $sourceMessageId)
                    ->exists();
                if ($exists) {
                    $stats['skipped']++;

                    continue;
                }

                // Fetch description + ticket URL + photo page link from event detail page
                $description = $fbEvent['description'] ?? '';
                $ticketUrl = '';
                $photoPageUrl = null;
                if (! empty($fbEvent['id'])) {
                    $eventUrl = "https://www.facebook.com/events/{$fbEvent['id']}/";
                    $details = $this->fetchEventDescription($eventUrl, $cookies);
                    if (empty($description)) {
                        $description = $details['description'] ?? '';
                    }
                    $ticketUrl = $details['ticket_url'] ?? '';
                    $photoPageUrl = $details['photo_page_url'] ?? null;
                }

                $event = Event::create([
                    'title' => Str::limit($fbEvent['name'], 100, ''),
                    'content' => $description ?: $fbEvent['name'],
                    'country' => $country,
                    'city' => $user->settings->city ?? config('facebook.default_city', 'yerevan'),
                    'location' => Str::limit($fbEvent['location'] ?: '—', 255, ''),
                    'type' => EventTypeEnum::CONCERT->value,
                    'start_date' => $fbEvent['start_date'],
                    'end_date' => $fbEvent['end_date'],
                    'start_time' => $fbEvent['start_time'],
                    'link' => $fbEvent['url'] ?? "https://www.facebook.com/{$username}/",
                    'ticket' => $ticketUrl,
                    'tg_source_chat_id' => $sourceChatId,
                    'tg_source_message_id' => $sourceMessageId,
                ]);
                $event->user_id = $user->id;
                $event->save();

                // Attach cover (try: photo page > listing page DOM > data URL)
                $imageAttached = false;
                if (! $imageAttached && $photoPageUrl) {
                    Log::debug('FacebookEventImport: Trying photo page', ['url' => $photoPageUrl]);
                    $photoImageUrl = $this->fetchPhotoPageImage($photoPageUrl, $cookies);
                    if ($photoImageUrl) {
                        Log::debug('FacebookEventImport: Photo page image found', ['url' => $photoImageUrl]);
                        $imageAttached = $this->attachCover($event, $photoImageUrl);
                    } else {
                        Log::debug('FacebookEventImport: Photo page image not found');
                    }
                }
                if (! $imageAttached && $fbEvent['image_url']) {
                    Log::debug('FacebookEventImport: Trying event image_url', ['url' => $fbEvent['image_url']]);
                    $imageAttached = $this->attachCover($event, $fbEvent['image_url']);
                }
                if (! $imageAttached) {
                    Log::warning('FacebookEventImport: No image attached', ['event' => $fbEvent['name']]);
                }

                $stats['imported']++;
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::error('FacebookEventImport: error creating event', [
                    'event_name' => $fbEvent['name'] ?? '?',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    // ---------------------------------------------------------------
    //  Private: Browsershot fetch (listing page)
    // ---------------------------------------------------------------

    private function fetchPageWithBrowser(string $url, array $cookies): ?array
    {
        try {
            $resultJson = Browsershot::url($url)
                ->setChromePath(config('browsershot.chrome_path', '/usr/bin/chromium-browser'))
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
                ->windowSize(1920, 1080)
                ->waitUntilNetworkIdle()
                ->setDelay(8000)
                ->timeout(60)
                ->setCookies($cookies)
                ->evaluate('
                    (async () => {
                        // Extract event posters from photo links (rendered DOM)
                        var eventImages = [];
                        document.querySelectorAll(\'a[href*="/photo/"] img[src*="fbcdn.net"]\').forEach(function(img) {
                            var src = img.getAttribute("src") || "";
                            if (src.indexOf("static.xx") !== -1) return;
                            if (src.indexOf("rsrc.php") !== -1) return;
                            eventImages.push({
                                src: src,
                                naturalWidth: img.naturalWidth || 0
                            });
                        });

                        // Also gather all non-static fbcdn imgs (to fill gaps)
                        var foundUrls = {};
                        eventImages.forEach(function(ev) { foundUrls[ev.src] = true; });
                        document.querySelectorAll(\'img[src*="fbcdn.net"]\').forEach(function(img) {
                            var src = img.getAttribute("src") || "";
                            if (src.indexOf("static.xx") !== -1) return;
                            if (src.indexOf("rsrc.php") !== -1) return;
                            if (foundUrls[src]) return;
                            var w = img.naturalWidth || img.width || 0;
                            if (w >= 200) {
                                eventImages.push({
                                    src: src,
                                    naturalWidth: w
                                });
                            }
                        });

                        return JSON.stringify({
                            html: document.documentElement.outerHTML,
                            event_images: eventImages
                        });
                    })();
                ');

            $parsed = json_decode($resultJson, true);
            if (! $parsed || empty($parsed['html'])) {
                Log::debug('FacebookEventImport: evaluate returned no HTML', ['url' => $url]);

                return null;
            }

            $html = $parsed['html'];
            $eventImages = $parsed['event_images'] ?? [];

            Log::debug('FacebookEventImport: page loaded via evaluate', [
                'url' => $url,
                'size' => strlen($html),
                'images_from_dom' => count($eventImages),
            ]);

            // Log all available images for debugging
            Log::debug('FacebookEventImport: Available DOM images', ['images' => $eventImages]);

            $events = $this->parseAllEventsFromHtml($html);

            // Enrich events with images from rendered DOM (DOM images are the actual event posters)
            if (! empty($events) && ! empty($eventImages)) {
                // Filter only high-quality images (min 500px width)
                $highQualityImages = array_filter($eventImages, function ($img) {
                    return ($img['naturalWidth'] ?? 0) >= 500;
                });
                $highQualityImages = array_values($highQualityImages);

                Log::debug('FacebookEventImport: High quality images', ['count' => count($highQualityImages), 'images' => $highQualityImages]);

                // If no high-quality images, use all images (fallback)
                $imagesToUse = ! empty($highQualityImages) ? $highQualityImages : $eventImages;

                foreach ($events as $i => $ev) {
                    if (isset($imagesToUse[$i])) {
                        $events[$i]['image_url'] = $imagesToUse[$i]['src'];
                        Log::debug('FacebookEventImport: DOM image assigned', [
                            'event' => $ev['name'],
                            'img_size' => $imagesToUse[$i]['naturalWidth'].'px',
                            'high_quality' => ($imagesToUse[$i]['naturalWidth'] ?? 0) >= 500,
                        ]);
                    }
                }
            }

            return $events;
        } catch (\Throwable $e) {
            Log::warning('FacebookEventImport: Browsershot error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    // ---------------------------------------------------------------
    //  Private: Description extraction from event detail page
    // ---------------------------------------------------------------

    /**
     * Fetch event description + ticket URL from the individual event page.
     * Returns ['description' => string, 'ticket_url' => string].
     */
    private function fetchEventDescription(string $eventUrl, array $cookies): array
    {
        try {
            $resultJson = Browsershot::url($eventUrl)
                ->setChromePath(config('browsershot.chrome_path', '/usr/bin/chromium-browser'))
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
                ->windowSize(1920, 1080)
                ->waitUntilNetworkIdle()
                ->setDelay(6000)
                ->timeout(45)
                ->setCookies($cookies)
                ->evaluate('
                    (async () => {
                        // Click all "See more" buttons to expand truncated descriptions
                        document.querySelectorAll(\'[role="button"]\').forEach(function(b) {
                            var t = (b.textContent || "").trim();
                            if (t.indexOf("See more") === 0 || t === "See more") {
                                b.click();
                            }
                        });

                        // Wait for React to expand
                        await new Promise(function(r) { setTimeout(r, 1500); });

                        // Now find span[dir="auto"] with multiple div children — event description
                        var spans = document.querySelectorAll(\'span[dir="auto"]\');
                        var bestDescription = "";
                        var bestLength = 0;

                        for (var si = 0; si < spans.length; si++) {
                            var span = spans[si];
                            var divs = span.querySelectorAll(\':scope > div\');
                            if (divs.length < 2) continue;

                            var fullText = span.textContent.trim();
                            if (fullText.length < 100) continue;

                            var parts = [];
                            for (var di = 1; di < divs.length; di++) {
                                var text = divs[di].textContent.trim();
                                if (text && text.indexOf("See ") !== 0 && text.indexOf("\u00b7") !== 0 && text.length > 10) {
                                    parts.push(text);
                                }
                            }

                            if (parts.length === 0) continue;

                            var combined = parts.join("\n\n");
                            if (combined.length > bestLength && combined.length > 50) {
                                bestLength = combined.length;
                                bestDescription = combined;
                            }
                        }

                        // Extract ticket URL from links (not Facebook, not login)
                        var ticketUrl = "";
                        var allAnchors = document.querySelectorAll(\'a[href]\');
                        for (var ai = 0; ai < allAnchors.length; ai++) {
                            var href = allAnchors[ai].getAttribute("href") || "";
                            // Must start with http and not be facebook
                            if (href.indexOf("http") !== 0) continue;
                            if (href.indexOf("facebook.com") !== -1) continue;
                            // Must contain ticket-related keywords
                            if (href.indexOf("tomsarkgh") !== -1 || href.indexOf("ticket") !== -1 || href.indexOf("buy") !== -1) {
                                ticketUrl = href;
                                break;
                            }
                        }

                        // Also check visible text with ticket URL patterns
                        if (!ticketUrl) {
                            var allSpans = document.querySelectorAll(\'span[dir="auto"]\');
                            for (var si2 = 0; si2 < allSpans.length; si2++) {
                                var t = allSpans[si2].textContent.trim();
                                // Must look like a URL, contain ticket keywords, not be facebook
                                if ((t.indexOf("http") === 0 || t.indexOf("www.") === 0) &&
                                    (t.indexOf("tomsarkgh") !== -1 || t.indexOf("ticket") !== -1 || t.indexOf("buy") !== -1) &&
                                    t.indexOf("facebook") === -1) {
                                    ticketUrl = t.indexOf("http") === 0 ? t : "https://" + t;
                                    break;
                                }
                            }
                        }

                        // Find photo page link (leads to full-size image)
                        var photoPageUrl = "";
                        var photoLink = document.querySelector(\'a[href*="/photo/"]\');
                        if (photoLink) {
                            photoPageUrl = photoLink.getAttribute("href") || "";
                            if (photoPageUrl && photoPageUrl.indexOf("http") !== 0) {
                                photoPageUrl = "https://www.facebook.com" + photoPageUrl;
                            }
                        }

                        return JSON.stringify({
                            description: bestDescription,
                            ticket_url: ticketUrl,
                            photo_page_url: photoPageUrl,
                            desc_length: bestLength
                        });
                    })();
                ');

            $parsed = json_decode($resultJson, true);
            $description = $parsed['description'] ?? '';
            $ticketUrl = $parsed['ticket_url'] ?? '';
            $photoPageUrl = $parsed['photo_page_url'] ?? '';

            if ($description) {
                Log::info('FacebookEventImport: event details fetched', [
                    'event' => basename($eventUrl),
                    'desc_len' => strlen($description),
                    'has_ticket' => ! empty($ticketUrl),
                    'has_photo_link' => ! empty($photoPageUrl),
                ]);
            }

            return [
                'description' => $description,
                'ticket_url' => $ticketUrl,
                'photo_page_url' => $photoPageUrl,
            ];
        } catch (\Throwable $e) {
            Log::debug('FacebookEventImport: failed to fetch event details', [
                'url' => $eventUrl,
                'error' => $e->getMessage(),
            ]);

            return ['description' => '', 'ticket_url' => ''];
        }
    }

    // ---------------------------------------------------------------
    //  Private: Photo page image extraction
    // ---------------------------------------------------------------

    /**
     * Navigate to the event photo page and extract the full-size image URL.
     * The photo page always shows the image at full resolution (1920x1005).
     */
    private function fetchPhotoPageImage(string $photoUrl, array $cookies): ?string
    {
        try {
            $resultJson = Browsershot::url($photoUrl)
                ->setChromePath(config('browsershot.chrome_path', '/usr/bin/chromium-browser'))
                ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'])
                ->windowSize(1920, 1080)
                ->waitUntilNetworkIdle()
                ->setDelay(4000)
                ->timeout(30)
                ->setCookies($cookies)
                ->evaluate('
                    (async () => {
                        // Find the largest non-static fbcdn image
                        var bestSrc = "";
                        var bestW = 0;
                        document.querySelectorAll(\'img[src*="fbcdn.net"]\').forEach(function(img) {
                            var src = img.getAttribute("src") || "";
                            if (src.indexOf("static.xx") !== -1) return;
                            if (src.indexOf("rsrc.php") !== -1) return;
                            var w = img.naturalWidth || 0;
                            if (w > bestW) {
                                bestW = w;
                                bestSrc = src;
                            }
                        });
                        return JSON.stringify({ src: bestSrc, width: bestW });
                    })();
                ');

            $parsed = json_decode($resultJson, true);
            $src = $parsed['src'] ?? '';

            if ($src) {
                Log::info('FacebookEventImport: photo page image found', [
                    'width' => $parsed['width'] ?? 0,
                ]);
            } else {
                Log::debug('FacebookEventImport: photo page image not found');
            }

            return $src ?: null;
        } catch (\Throwable $e) {
            Log::debug('FacebookEventImport: photo page failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    // ---------------------------------------------------------------
    //  Private: HTML parsing
    // ---------------------------------------------------------------

    private function parseAllEventsFromHtml(string $html): array
    {
        $all = [];

        $events = $this->parseFacebookJson($html);
        foreach ($events as $ev) {
            $all[$ev['_key']] = $ev;
        }

        if (! empty($all)) {
            $eventImages = $this->extractEventImagesFromHtml($html);
            foreach ($all as $key => $ev) {
                $name = $ev['name'];
                $bestMatch = null;
                $bestDist = PHP_INT_MAX;

                foreach ($eventImages as $img) {
                    $namePos = strpos($html, $name);
                    $imgPos = strpos($html, $img['raw_url']);

                    if ($namePos !== false && $imgPos !== false) {
                        $dist = abs($namePos - $imgPos);
                        if ($dist < $bestDist) {
                            $bestDist = $dist;
                            $bestMatch = $img;
                        }
                    }

                    if ($img['alt'] && stripos($img['alt'], $name) !== false) {
                        $bestMatch = $img;
                        break;
                    }
                }

                if ($bestMatch && $bestDist < 8000) {
                    $all[$key]['image_url'] = $bestMatch['url'];
                }

                if (empty($all[$key]['image_url']) && ! empty($ev['image_url'])) {
                    $all[$key]['image_url'] = $ev['image_url'];
                }
            }

            return array_values($all);
        }

        $events = $this->parseJsonLd($html);
        foreach ($events as $ev) {
            $all[$ev['_key']] = $ev;
        }
        if (! empty($all)) {
            return array_values($all);
        }

        $events = $this->parseRenderedHtml($html);
        foreach ($events as $ev) {
            $all[$ev['_key']] = $ev;
        }

        return array_values($all);
    }

    private function extractEventImagesFromHtml(string $html): array
    {
        $images = [];
        preg_match_all('/<img[^>]*src=["\']([^"\']*fbcdn\.net[^"\']*)["\'][^>]*>/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $rawUrl = $match[1];
            $decodedUrl = html_entity_decode($rawUrl);
            $baseUrl = preg_replace('/\?.*/', '', $decodedUrl);

            $alt = '';
            if (preg_match('/alt=["\']([^"\']*)["\']/i', $match[0], $altMatch)) {
                $alt = trim($altMatch[1]);
            }

            if (! isset($images[$baseUrl])) {
                $images[$baseUrl] = [
                    'raw_url' => $rawUrl,
                    'url' => $decodedUrl,
                    'alt' => $alt,
                ];
            }
        }

        return array_values($images);
    }

    private function parseFacebookJson(string $html): array
    {
        $events = [];
        $search = '__typename":"Event"';
        $htmlLen = strlen($html);
        $pos = 0;
        $seen = [];

        while (($pos = strpos($html, $search, $pos)) !== false) {
            if ($pos > $htmlLen) {
                break;
            }

            $start = $pos;
            $braceCount = 0;
            for ($i = $pos; $i >= 0; $i--) {
                if ($html[$i] === '}') {
                    $braceCount++;
                } elseif ($html[$i] === '{') {
                    $braceCount--;
                    if ($braceCount < 0) {
                        $start = $i;
                        $braceCount = 0;
                        break;
                    }
                }
            }

            $end = $start + 1;
            $braceCount = 1;
            for ($i = $start + 1; $i < $htmlLen; $i++) {
                if ($html[$i] === '{') {
                    $braceCount++;
                } elseif ($html[$i] === '}') {
                    $braceCount--;
                    if ($braceCount === 0) {
                        $end = $i + 1;
                        break;
                    }
                }
            }

            $json = substr($html, $start, $end - $start);

            try {
                $data = json_decode($json, true, 32, JSON_INVALID_UTF8_IGNORE);
                if (is_array($data) && ! empty($data['name'])) {
                    $isEvent = ($data['__typename'] ?? '') === 'Event'
                        || ! empty($data['day_time_sentence']);

                    if ($isEvent) {
                        $normalized = $this->normalizeFacebookEvent($data);
                        if ($normalized && ! isset($seen[$normalized['_key']])) {
                            $seen[$normalized['_key']] = true;
                            $events[$normalized['_key']] = $normalized;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // skip invalid JSON
            }

            $pos = $end + 1;
        }

        return array_values($events);
    }

    /**
     * Deep search for Facebook Event objects in nested data.
     */
    private function searchFacebookEvents(array $data, int $depth = 0): array
    {
        if ($depth > 8) {
            return [];
        }

        $events = [];

        foreach ($data as $value) {
            if (! is_array($value)) {
                continue;
            }

            $isEvent = ($value['__typename'] ?? '') === 'Event'
                && ! empty($value['name'])
                && (! empty($value['day_time_sentence']) || ! empty($value['start_time']) || ! empty($value['startDate']));

            if ($isEvent) {
                $normalized = $this->normalizeFacebookEvent($value);
                if ($normalized) {
                    $events[$normalized['_key']] = $normalized;
                }
            }

            if (! $isEvent && ! empty($value['name']) && ! empty($value['day_time_sentence'])) {
                $normalized = $this->normalizeFacebookEvent($value);
                if ($normalized) {
                    $events[$normalized['_key']] = $normalized;
                }
            }

            $sub = $this->searchFacebookEvents($value, $depth + 1);
            foreach ($sub as $ev) {
                $events[$ev['_key']] = $ev;
            }
        }

        return $events;
    }

    private function normalizeFacebookEvent(array $data): ?array
    {
        $name = $data['name'] ?? null;
        if (! $name || ! is_string($name)) {
            return null;
        }

        $dayTime = $data['day_time_sentence'] ?? '';
        $startTime = $data['start_time'] ?? $data['startDate'] ?? null;

        $startDate = null;
        $startTimeOnly = null;
        if ($dayTime) {
            $cleanDayTime = preg_replace('/\R|[\x{202f}\x{00a0}\x{2009}]/u', ' ', $dayTime);
            $cleanDayTime = trim(preg_replace('/\s+/', ' ', $cleanDayTime));

            if (preg_match('/\w{3}, (\w{3} \d+)/', $cleanDayTime, $dateMatch)) {
                $dateStr = $dateMatch[1];
                try {
                    $dt = new \DateTime($dateStr);
                    $startDate = $dt->format('Y-m-d');
                } catch (\Throwable $e) {
                    // fallback
                }
            }

            if (preg_match('/(\d+:\d+)\s*([AP]M)/i', $cleanDayTime, $timeMatch)) {
                try {
                    $dt = new \DateTime($timeMatch[1].' '.$timeMatch[2]);
                    $startTimeOnly = $dt->format('H:i');
                } catch (\Throwable $e) {
                    // fallback
                }
            }
        } elseif ($startTime) {
            $startDate = date('Y-m-d', strtotime((string) $startTime)) ?: null;
            $startTimeOnly = date('H:i', strtotime((string) $startTime)) ?: null;
        }

        $location = '—';
        $place = $data['event_place'] ?? null;
        if (is_array($place)) {
            $location = $place['contextual_name'] ?? $place['name'] ?? '—';
        }

        $imageUrl = null;
        $image = $data['image'] ?? null;
        if (is_array($image)) {
            $imageUrl = $image['uri'] ?? $image['url'] ?? null;
        } elseif (is_string($image)) {
            $imageUrl = $image;
        }

        $eventUrl = $data['url'] ?? null;
        $eventId = $data['id'] ?? null;

        return [
            'id' => $eventId,
            'name' => strip_tags((string) $name),
            'description' => '',
            'start_date' => $startDate,
            'end_date' => null,
            'start_time' => $startTimeOnly,
            'location' => $location,
            'image_url' => $imageUrl,
            'url' => $eventUrl,
            '_key' => md5($name.($startDate ?? '').($eventId ?? '')),
        ];
    }

    private function parseJsonLd(string $html): array
    {
        $events = [];
        preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);
        if (empty($matches[1])) {
            return $events;
        }
        foreach ($matches[1] as $raw) {
            $data = json_decode(trim($raw), true);
            if (! is_array($data)) {
                continue;
            }
            foreach (['@graph', '@set'] as $key) {
                if (isset($data[$key]) && is_array($data[$key])) {
                    foreach ($data[$key] as $item) {
                        $ev = $this->normalizeLdEvent($item);
                        if ($ev) {
                            $events[$ev['_key']] = $ev;
                        }
                    }
                }
            }
            $ev = $this->normalizeLdEvent($data);
            if ($ev) {
                $events[$ev['_key']] = $ev;
            }
        }

        return array_values($events);
    }

    private function parseRenderedHtml(string $html): array
    {
        $events = [];
        $seen = [];

        preg_match_all('/<a[^>]*href=["\'](https?:\/\/[^"\']*\/events\/[^"\']*)["\'][^>]*>([^<]+)<\/a>/is', $html, $matches);

        foreach ($matches[2] as $i => $name) {
            $name = trim(strip_tags($name));
            $url = $matches[1][$i] ?? null;

            if ($name && strlen($name) > 3 && ! isset($seen[$name])) {
                $seen[$name] = true;
                $events[] = [
                    'id' => null,
                    'name' => $name,
                    'description' => '',
                    'start_date' => null,
                    'end_date' => null,
                    'start_time' => null,
                    'location' => '—',
                    'image_url' => null,
                    'url' => $url,
                    '_key' => md5($name),
                ];
            }
        }

        return $events;
    }

    private function normalizeLdEvent(array $data): ?array
    {
        if (($data['@type'] ?? '') !== 'Event') {
            return null;
        }
        $name = $data['name'] ?? null;
        $startDate = $data['startDate'] ?? $data['start_date'] ?? null;
        if (! $name || ! $startDate) {
            return null;
        }
        $endDate = $data['endDate'] ?? $data['end_date'] ?? null;
        $description = $data['description'] ?? $name;
        $locationName = '—';
        $location = $data['location'] ?? null;
        if (is_array($location)) {
            $locationName = $location['name'] ?? '—';
            $address = $location['address'] ?? null;
            if (is_array($address)) {
                $parts = array_filter([
                    $address['streetAddress'] ?? null,
                    $address['addressLocality'] ?? null,
                    $address['addressCountry'] ?? null,
                ]);
                if (! empty($parts)) {
                    $locationName .= ' ('.implode(', ', $parts).')';
                }
            }
        }
        $imageUrl = null;
        $image = $data['image'] ?? null;
        if (is_string($image)) {
            $imageUrl = $image;
        } elseif (is_array($image)) {
            $imageUrl = $image[0] ?? $image['url'] ?? null;
        }
        $eventUrl = $data['url'] ?? null;

        return [
            'id' => null,
            'name' => strip_tags((string) $name),
            'description' => strip_tags((string) $description),
            'start_date' => date('Y-m-d', strtotime((string) $startDate)) ?: null,
            'end_date' => $endDate ? (date('Y-m-d', strtotime((string) $endDate)) ?: null) : null,
            'start_time' => date('H:i', strtotime((string) $startDate)) ?: null,
            'location' => $locationName,
            'image_url' => $imageUrl,
            'url' => $eventUrl ?: null,
            '_key' => md5($name.$startDate),
        ];
    }

    // ---------------------------------------------------------------
    //  Image download methods
    // ---------------------------------------------------------------

    private function attachCover(Event $event, string $imageUrl): bool
    {
        try {
            Log::debug('FacebookEventImport: Attempting to download cover', ['url' => $imageUrl]);
            $response = Http::timeout(15)
                ->withHeaders([
                    'Referer' => 'https://www.facebook.com/',
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])
                ->get($imageUrl);
            if (! $response->successful()) {
                Log::warning('FacebookEventImport: HTTP cover download failed', ['status' => $response->status()]);

                return false;
            }
            $contents = $response->body();
            if (empty($contents)) {
                Log::warning('FacebookEventImport: Empty image content');

                return false;
            }
            $tmp = tempnam(sys_get_temp_dir(), 'fb_cover_');
            file_put_contents($tmp, $contents);
            $event->addMedia($tmp)
                ->usingFileName('fb_event_'.time().'.jpg')
                ->toMediaCollection('poster');
            Log::info('FacebookEventImport: Cover attached successfully', ['event_id' => $event->id]);

            return true;
        } catch (\Throwable $e) {
            Log::error('FacebookEventImport: could not download cover', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return false;
        }
    }
}
