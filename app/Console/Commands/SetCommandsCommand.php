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
        $menu = trans('commands');
        dd($menu);
        $menu = array_merge($menu, ['add_event_instruction' => trans('commands.add_event_instruction')]);

        $menuButtons = array_map(function ($value) {
            return trans($value);
        }, $menu);

        Bot::firstOrFail()->registerCommands($menuButtons)->send();
    }
}
