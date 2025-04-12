<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('events.index');
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

Route::get('verification', EmailVerificationPromptController::class)
    ->name('verification.notice');
Route::get('profile/{username}', [ProfileController::class, 'index'])->name(
    'profile.show'
);

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

require_once __DIR__.'/guest.php';
require_once __DIR__.'/auth.php';
