<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Guest\BandController;
use App\Http\Controllers\Guest\BlogController;
use App\Http\Controllers\Guest\CommunityController;
use App\Http\Controllers\Guest\EventController;
use App\Http\Controllers\Guest\GalleryController;
use App\Http\Controllers\Guest\GoogleAuthController;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\PwaInstallController;
use App\Http\Controllers\Profile\ProfileController;
use Inertia\Inertia;

require_once __DIR__.'/guest.php';
require_once __DIR__.'/auth.php';

Route::get('/test', function () {
    return Inertia::render('Test');
});


Route::get('/', HomeController::class)->name('home');
Route::get('events/past', [EventController::class, 'past'])->name('events.past');
Route::resource('events', EventController::class)->only('index', 'show');

Route::resource('bands', BandController::class)->only('index');
Route::get('bands/{band:slug}', [BandController::class, 'show'])
    ->name('bands.show');
Route::get('blogs/{blog:slug}', [BlogController::class, 'show'])
    ->name('blogs.show');
Route::resource('galleries', GalleryController::class)->only('index', 'show');
Route::resource('community', CommunityController::class)->only('index', 'show');

Route::post('pwa-install', PwaInstallController::class)->name('pwa.install');

Route::get('verification', EmailVerificationPromptController::class)
    ->name('verification.notice');
Route::get('profile/{username}', [ProfileController::class, 'index'])->name(
    'profile.show'
);

Route::prefix('auth')->group(function () {
    Route::get('/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::get('privacy', function () {
    return Inertia::render('Privacy');
});

Route::fallback(function () {
    return Inertia::render('404');
});
