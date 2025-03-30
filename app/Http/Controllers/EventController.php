<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    public function index()
    {
        $response = Http::get('https://metal-events-bot.mpetrosyan.com/api/event');
        dd($response);
    }
}
