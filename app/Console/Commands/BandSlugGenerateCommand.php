<?php

namespace App\Console\Commands;

use App\Models\Band;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BandSlugGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:band-slug-generate';

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
        $bands = Band::whereNull('slug')->get();

        foreach ($bands as $band) {
            $band->slug = Str::slug($band->name);
            $band->save();
        }

        $this->info('Slugs generated successfully.');
    }
}
