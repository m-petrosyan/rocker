<?php

namespace App\Console\Commands;

use App\Services\TelegramPostImportService;
use Illuminate\Console\Command;

class TestTelegramImportCommand extends Command
{
    /**
     * Тестирует TelegramPostImportService без реального webhook.
     *
     * Примеры:
     *   php artisan app:test-telegram-import
     *      → берёт первый source из config('telegram_sources.channels')
     *
     *   php artisan app:test-telegram-import --source=-1001583147579:97732
     *      → конкретный chat:thread
     *
     *   php artisan app:test-telegram-import --json=/tmp/post.json
     *      → импорт payload-а из JSON (как пришло бы из webhook'а)
     *
     *   php artisan app:test-telegram-import --file-id=AgACAgIAAxk...
     *      → реальный file_id из Telegram — будет попытка скачать фото
     */
    protected $signature = 'app:test-telegram-import
        {--source= : ключ из config/telegram_sources.php (chat_id:thread_id)}
        {--message-id= : тестовый telegram message_id (по умолчанию time())}
        {--caption= : текст поста (по умолчанию заготовка)}
        {--file-id= : реальный photo file_id чтобы протестить скачивание}
        {--json= : путь к JSON-файлу с готовым payload (перекрывает остальные опции)}';

    protected $description = 'Прогнать TelegramPostImportService с фейковым/реальным payload';

    public function handle(TelegramPostImportService $service): int
    {
        $payload = $this->buildPayload();

        $this->info('Payload:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $event = $service->import($payload);

        if (! $event) {
            $this->warn('Event не создан (см. лог storage/logs/laravel.log)');

            return self::FAILURE;
        }

        $this->info("✅ Event id={$event->id} создан со статусом pending");
        $this->line("Title: {$event->title}");
        $this->line("Country/City: {$event->country}/{$event->city}");

        return self::SUCCESS;
    }

    private function buildPayload(): array
    {
        if ($jsonPath = $this->option('json')) {
            $raw = file_get_contents($jsonPath);
            $decoded = json_decode($raw, true);

            return $decoded['message']
                ?? $decoded['channel_post']
                ?? $decoded;
        }

        $sources = config('telegram_sources.channels', []);
        $sourceKey = $this->option('source') ?: array_key_first($sources);

        if (! isset($sources[$sourceKey])) {
            $this->error("Source `$sourceKey` не найден в config/telegram_sources.php");
            exit(self::FAILURE);
        }

        [$chatId, $threadId] = array_pad(explode(':', $sourceKey, 2), 2, null);

        $caption = $this->option('caption')
            ?: "🧪 Тестовый импорт поста\n\nЭто фейковое сообщение чтобы проверить TelegramPostImportService — должен создаться Event со статусом pending и уведомление админу.";

        $payload = [
            'message_id' => (int) ($this->option('message-id') ?: time()),
            'chat' => [
                'id' => (int) $chatId,
                'type' => 'supergroup',
            ],
            'caption' => $caption,
            'photo' => [
                [
                    'file_id' => $this->option('file-id') ?: 'FAKE_FILE_ID_FOR_TEST',
                    'file_size' => 100000,
                    'width' => 1280,
                    'height' => 720,
                ],
            ],
        ];

        if ($threadId) {
            $payload['message_thread_id'] = (int) $threadId;
        }

        return $payload;
    }
}
