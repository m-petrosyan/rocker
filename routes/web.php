<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Soon');
})->name('home');

Route::resource('/events', EventController::class)->only('index', 'show');

Route::get(
    '/gallery',
    function () {
        return Inertia::render('Gallery/Gallery', [
            'galleries' => 'some data',
        ]);
    }
)->name('gallery');


require_once __DIR__.'/guest.php';
require_once __DIR__.'/auth.php';
