<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
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


Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', function () {
        return Inertia::render('Profile/Profile');
    })->name('profile.index');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
