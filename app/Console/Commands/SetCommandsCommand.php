<?php

namespace App\Console\Commands;

use App\Models\Bot;
use Illuminate\Console\Command;

class SetCommandsCommand extends Command
{
    protected $signature = 'app:set-commands';

    protected $description = 'Register Telegram bot commands';

    public function handle(): void
    {
        Bot::firstOrFail()->registerCommands(trans('commands'))->send();
    }
}
