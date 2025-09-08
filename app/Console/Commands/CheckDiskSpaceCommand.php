<?php

namespace App\Console\Commands;

use App\Models\UserBot;
use Illuminate\Console\Command;

class CheckDiskSpaceCommand extends Command
{
    protected $signature = 'disk:check';
    protected $description = 'Проверяет заполненность диска и отправляет уведомление в Telegram, если занято более 90%';

    public function handle(): void
    {
        $drive = '/';
        $usedSpace = (1 - disk_free_space($drive) / disk_total_space($drive)) * 100;

        $prcent = 80;

        if ($usedSpace >= $prcent) {
            $chat = UserBot::where('chat_id', config('telegraph.webhook.chat_id'))->firstOrFail();

            $chat->message("⚠️ Disk is ".(int)$usedSpace."%  full!")->send();

            $this->info('Notification sent to Telegram.');
        } else {
            $this->info("Disk is less than ".$prcent."% full.");
        }
    }
}
