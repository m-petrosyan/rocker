<?php

namespace App\Console\Commands;

use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Console\Command;

class CheckDiskSpace extends Command
{
    protected $signature = 'disk:check';
    protected $description = 'Проверяет заполненность диска и отправляет уведомление в Telegram, если занято более 90%';

    public function handle()
    {
        $drive = '/';
        $usedSpace = (1 - disk_free_space($drive) / disk_total_space($drive)) * 100;

        $prcent = 5;
        dump($usedSpace);
        if ($usedSpace >= $prcent) {
            Telegraph::message("⚠️ Диск заполнен на {$usedSpace}%!")
                ->chatId(config('telegraph.webhook.chat_id'))
                ->send();

            $this->info('Уведомление отправлено в Telegram.');
        } else {
            $this->info("Диск заполнен менее чем на $prcent %.");
        }
    }
}
