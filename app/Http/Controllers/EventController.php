<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        $response = Http::get('https://metal-events-bot.mpetrosyan.com/api/event');

        return Inertia::render('Events', [
            'events' => $response->json(),
        ]);
    }
}
