<?php

namespace App\Services;

use App\Enums\EventTypeEnum;
use App\Jobs\AdminPendingEventNotificationJob;
use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramPostImportService
{
    /**
     * Принимает payload `message` или `channel_post` из Telegram webhook
     * и создаёт Event со статусом pending, если:
     *   - чат + thread входят в config('telegram_sources.channels')
     *   - в сообщении есть фото и текст (caption)
     *   - такого telegram-сообщения ещё нет в БД
     */
    public function import(array $payload): ?Event
    {
        $chatId = (string) ($payload['chat']['id'] ?? '');
        $threadId = $payload['message_thread_id'] ?? null;
        $messageId = $payload['message_id'] ?? null;

        if (! $chatId || ! $messageId) {
            return null;
        }

        $source = $this->matchSource($chatId, $threadId);
        if (! $source) {
            return null;
        }

        $caption = trim($payload['caption'] ?? '');
        $photos = $payload['photo'] ?? [];

        $minLength = (int) config('telegram_sources.min_caption_length', 30);
        if ($caption === '' || mb_strlen($caption) < $minLength || empty($photos)) {
            Log::info('TelegramPostImport: skip — нет фото/текста или короткая подпись', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
            ]);

            return null;
        }

        $exists = Event::query()
            ->where('tg_source_chat_id', $chatId)
            ->where('tg_source_message_id', $messageId)
            ->exists();
        if ($exists) {
            return null;
        }

        $event = Event::create([
            'title' => Str::limit(strip_tags($caption), 100, ''),
            'content' => $caption,
            'country' => $source['country'],
            'city' => $source['city'],
            'location' => '—',
            'type' => EventTypeEnum::EVENT->value,
            'link' => $this->buildMessageLink($chatId, $messageId),
            'tg_source_chat_id' => $chatId,
            'tg_source_message_id' => $messageId,
        ]);

        $this->attachPoster($event, $photos);

        dispatch(new AdminPendingEventNotificationJob($event->id));

        Log::info('TelegramPostImport: создан pending Event', [
            'event_id' => $event->id,
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);

        return $event;
    }

    private function matchSource(string $chatId, ?int $threadId): ?array
    {
        $channels = config('telegram_sources.channels', []);

        $keys = [];
        if ($threadId) {
            $keys[] = $chatId.':'.$threadId;
        }
        $keys[] = $chatId;

        foreach ($keys as $key) {
            if (isset($channels[$key])) {
                return $channels[$key];
            }
        }

        return null;
    }

    /**
     * Формирует ссылку на пост в Telegram.
     * Для супергрупп/каналов: https://t.me/c/{chat_id}/{message_id}
     */
    private function buildMessageLink(string $chatId, int $messageId): string
    {
        $chatId = preg_replace('/^-100/', '', $chatId);

        return "https://t.me/c/{$chatId}/{$messageId}";
    }

    private function attachPoster(Event $event, array $photos): void
    {
        $largest = collect($photos)->sortByDesc('file_size')->first();
        $fileId = $largest['file_id'] ?? null;

        if (! $fileId) {
            return;
        }

        try {
            $token = config('telegraph.configs.token');
            $apiUrl = rtrim(config('telegraph.telegram_api_url', 'https://api.telegram.org/'), '/');

            $info = Http::asJson()
                ->post("{$apiUrl}/bot{$token}/getFile", ['file_id' => $fileId])
                ->throw()
                ->json();

            $filePath = $info['result']['file_path'] ?? null;
            if (! $filePath) {
                return;
            }

            $contents = Http::get("{$apiUrl}/file/bot{$token}/{$filePath}")
                ->throw()
                ->body();

            $tmp = tempnam(sys_get_temp_dir(), 'tg_poster_');
            file_put_contents($tmp, $contents);

            $event->addMedia($tmp)
                ->usingFileName(basename($filePath))
                ->toMediaCollection('poster');
        } catch (\Throwable $e) {
            Log::error('TelegramPostImport: не удалось скачать фото', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
