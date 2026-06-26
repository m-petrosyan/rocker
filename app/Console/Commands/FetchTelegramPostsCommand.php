<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\TelegramPostImportService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class FetchTelegramPostsCommand extends Command
{
    /**
     * Получает реальные последние посты из Telegram группы через Bot API (getUpdates)
     * и прогоняет их через TelegramPostImportService.
     *
     * Примеры:
     *   php artisan app:fetch-telegram-posts
     *      → интерактивный выбор группы, 5 последних обновлений
     *
     *   php artisan app:fetch-telegram-posts --source=yerevanmetal --limit=10
     *      → 10 постов из группы yerevanmetal
     *
     *   php artisan app:fetch-telegram-posts --delete-webhook --wait=60
     *      → отключает webhook, ждёт новый пост до 60 секунд, создаёт Event,
     *        восстанавливает webhook
     *
     *   php artisan app:fetch-telegram-posts --token=123456:ABC-DEF1234ghIkl --limit=3
     *      → 3 поста через отдельного тестового бота
     *
     * @see https://core.telegram.org/bots/api#getupdates
     */
    protected $signature = 'app:fetch-telegram-posts
        {--source= : Ключ из config/telegram_sources.php (name или chat_id:thread_id)}
        {--limit=5 : Количество последних постов для обработки}
        {--token= : Токен отдельного тестового бота (если стоит production webhook)}
        {--delete-webhook : Временно отключить webhook для getUpdates (будет восстановлен)}
        {--wait= : Ждать новые посты N секунд (long polling). Работает с --delete-webhook}';

    protected $description = 'Получить последние посты из Telegram группы через Bot API и создать Events';

    public function handle(TelegramPostImportService $service): int
    {
        $sourceKey = $this->resolveSourceKey();
        if ($sourceKey === null) {
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        if ($limit < 1) {
            $this->error('--limit должен быть больше 0');

            return self::FAILURE;
        }

        $wait = $this->option('wait');

        $chatId = $this->parseChatId($sourceKey);
        $token = $this->option('token') ?: config('telegraph.configs.token');
        $apiUrl = rtrim(config('telegraph.telegram_api_url', 'https://api.telegram.org/'), '/');

        // Управление webhook
        $savedWebhookUrl = null;
        if ($this->option('delete-webhook')) {
            $savedWebhookUrl = $this->getWebhookUrl($token, $apiUrl);
            if ($savedWebhookUrl) {
                $this->warn("⚠️  Временно отключаю webhook: {$savedWebhookUrl}");
                $this->deleteWebhook($token, $apiUrl);
            } else {
                $this->line('Webhook не найден, пропускаю отключение.');
            }
        } elseif (! $this->option('token')) {
            if (! $this->warnUnlessWebhookInactive($token, $apiUrl)) {
                return self::SUCCESS;
            }
        }

        // Ожидание новых постов (long polling)
        $waitSeconds = $wait !== null ? (int) $wait : 0;

        if ($waitSeconds > 0 && $this->option('delete-webhook')) {
            $this->info("👀 Ожидаю новый пост в группе до {$waitSeconds} секунд...");
            $this->line('   Отправьте пост с фото и текстом в Telegram группу сейчас.');
            $this->line('');
        }

        // Получаем обновления
        $updates = $this->fetchUpdates($token, $apiUrl, $chatId, $limit, $waitSeconds);

        // Восстанавливаем webhook (даже если fetchUpdates упал)
        if ($savedWebhookUrl) {
            $this->restoreWebhook($token, $apiUrl, $savedWebhookUrl);
        }

        if ($updates === null) {
            return self::SUCCESS;
        }

        // Обрабатываем сообщения
        return $this->processMessages($service, $updates, $chatId, $limit);
    }

    // ─── Webhook management ───────────────────────────────────────────────

    private function getWebhookUrl(string $token, string $apiUrl): ?string
    {
        try {
            $response = Http::timeout(10)
                ->get("{$apiUrl}/bot{$token}/getWebhookInfo")
                ->throw()
                ->json();

            $url = $response['result']['url'] ?? '';

            return $url !== '' ? $url : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function deleteWebhook(string $token, string $apiUrl): void
    {
        Http::timeout(10)
            ->post("{$apiUrl}/bot{$token}/deleteWebhook")
            ->throw();
    }

    private function restoreWebhook(string $token, string $apiUrl, string $url): void
    {
        $this->line('Восстанавливаю webhook...');

        try {
            Http::timeout(10)
                ->post("{$apiUrl}/bot{$token}/setWebhook", [
                    'url' => $url,
                ])
                ->throw();

            $this->info("✅ Webhook восстановлен: {$url}");
        } catch (\Throwable $e) {
            $this->error("❌ Не удалось восстановить webhook: {$e->getMessage()}");
            $this->line("   URL для ручного восстановления: {$url}");
        }
    }

    private function warnUnlessWebhookInactive(string $token, string $apiUrl): bool
    {
        $hasWebhook = $this->getWebhookUrl($token, $apiUrl) !== null;

        if ($hasWebhook) {
            $this->warn('⚠️  У этого бота активен webhook! getUpdates НЕ будет работать.');
            $this->line('   Используйте --delete-webhook чтобы временно отключить его,');
            $this->line('   или --token для отдельного тестового бота.');
            $this->line('');

            return $this->confirm('Продолжить? (скорее всего обновлений не будет)', false);
        }

        return true;
    }

    // ─── Fetch updates ────────────────────────────────────────────────────

    private function fetchUpdates(string $token, string $apiUrl, string $chatId, int $limit, int $waitSeconds = 0): ?array
    {
        $pollTimeout = $waitSeconds > 0 ? $waitSeconds : 10;
        $httpTimeout = $pollTimeout + 20; // HTTP таймаут должен быть больше polling

        $this->info("Запрашиваю обновления для чата {$chatId} (polling {$pollTimeout}c)...");

        try {
            $response = Http::timeout($httpTimeout)
                ->asJson()
                ->post("{$apiUrl}/bot{$token}/getUpdates", [
                    'timeout' => $pollTimeout,
                    'limit' => min($limit * 2, 100),
                    'allowed_updates' => ['channel_post', 'message'],
                ])
                ->throw()
                ->json();
        } catch (RequestException $e) {
            $status = $e->response->status();
            $body = $e->response->body();

            if ($status === 409) {
                $this->error('❌ Telegram вернул 409 Conflict: getUpdates не работает пока активен webhook.');
                $this->line('   Используйте --delete-webhook чтобы временно отключить webhook.');
            } else {
                $this->error("❌ Ошибка Telegram API (HTTP {$status}): {$body}");
            }

            return null;
        }

        $updates = $response['result'] ?? [];

        if (empty($updates)) {
            $this->warn('Нет обновлений от Telegram.');
            $this->line('- Если используете --wait: не было новых постов за это время');
            $this->line('- Если без --wait: нет непрочитанных обновлений на сервере');
            $this->line('- Бот имеет права на чтение сообщений? (админ или Privacy Mode отключён)');

            return null;
        }

        $messages = $this->extractMessages($updates, $chatId);

        if (empty($messages)) {
            $this->warn("Постов из чата {$chatId} не найдено среди обновлений.");

            return null;
        }

        $this->info('Найдено сообщений в чате: '.count($messages));

        return $messages;
    }

    // ─── Process messages ─────────────────────────────────────────────────

    private function processMessages(TelegramPostImportService $service, array $messages, string $chatId, int $limit): int
    {
        $this->line('');

        // Берём последние $limit штук (getUpdates возвращает от старых к новым)
        $recent = array_slice($messages, -$limit);

        $processed = 0;
        $skipped = 0;

        foreach ($recent as $message) {
            $event = $service->import($message);

            if ($event) {
                $processed++;
                $this->line("✅ Event #{$event->id} — «{$event->title}»");
            } else {
                $skipped++;
                $msgId = $message['message_id'] ?? '?';
                $reason = $this->whySkipped($message);
                $this->line("⏭️  Сообщение #{$msgId} — {$reason}");
            }
        }

        $this->newLine();
        $this->info("Готово: создано {$processed} Events, пропущено {$skipped}");

        return self::SUCCESS;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    private function resolveSourceKey(): ?string
    {
        $sources = config('telegram_sources.channels', []);

        if (empty($sources)) {
            $this->error('Нет настроенных источников в config/telegram_sources.php');

            return null;
        }

        $sourceOption = $this->option('source');

        if ($sourceOption) {
            foreach ($sources as $key => $source) {
                if (($source['name'] ?? null) === $sourceOption) {
                    return $key;
                }
            }

            if (isset($sources[$sourceOption])) {
                return $sourceOption;
            }

            $this->error("Source `{$sourceOption}` не найден в config/telegram_sources.php");

            return null;
        }

        $keys = array_keys($sources);
        $labels = [];
        foreach ($sources as $source) {
            $label = $source['name'];
            if (isset($source['city'])) {
                $label .= " ({$source['city']}, {$source['country']})";
            }
            $labels[] = $label;
        }

        $selectedLabel = $this->choice('Выберите Telegram группу', $labels, 0);

        return $keys[array_search($selectedLabel, $labels)];
    }

    private function parseChatId(string $sourceKey): string
    {
        return explode(':', $sourceKey, 2)[0];
    }

    private function extractMessages(array $updates, string $targetChatId): array
    {
        $messages = [];

        foreach ($updates as $update) {
            $post = $update['channel_post'] ?? $update['message'] ?? null;
            if ($post === null) {
                continue;
            }

            $updateChatId = (string) ($post['chat']['id'] ?? '');
            if ($updateChatId !== $targetChatId) {
                continue;
            }

            $messages[] = $post;
        }

        return $messages;
    }

    private function whySkipped(array $message): string
    {
        $caption = trim($message['caption'] ?? '');
        $minLength = (int) config('telegram_sources.min_caption_length', 30);

        if (empty($message['photo'] ?? [])) {
            return 'нет фото';
        }

        if ($caption === '') {
            return 'нет текста (caption)';
        }

        if (mb_strlen($caption) < $minLength) {
            return 'короткий текст ('.mb_strlen($caption)." < {$minLength} символов)";
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $msgId = $message['message_id'] ?? 0;

        if (Event::query()
            ->where('tg_source_chat_id', $chatId)
            ->where('tg_source_message_id', $msgId)
            ->exists()
        ) {
            return 'уже импортирован';
        }

        return 'другая причина (см. лог)';
    }
}
