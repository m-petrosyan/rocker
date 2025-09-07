<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RobotsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:robots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a robots.txt file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /profile\n";
        $content .= "Disallow: /forgot-password\n";
        $content .= "Sitemap: ".url('/sitemap.xml')."\n";
        $content .= "Host: ".url('/')."\n";

        File::put(public_path('robots.txt'), $content);
    }
}
