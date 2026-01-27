<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\PwaInstall;

class PwaInstallController extends Controller
{
    public function __invoke(): void
    {
        PwaInstall::query()->create();
    }
}
