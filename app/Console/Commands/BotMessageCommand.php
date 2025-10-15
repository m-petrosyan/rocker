<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BotMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bot-message-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        User::findOrFail(22)->load('chat')->chat->message('accept')->send();
    }
}
