<?php

namespace App\Http\Controllers;

use App\Models\PwaInstall;

class PwaInstallController extends Controller
{
    public function __invoke(): void
    {
        PwaInstall::query()->create();
    }
}
