<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PwaInstallController;
use App\Models\UserBot;
use Illuminate\Http\Request;
use Inertia\Inertia;

require_once __DIR__.'/guest.php';
require_once __DIR__.'/auth.php';

Route::get('/test', function () {
    dd(1);

    return Inertia::render('Test');
});


Route::post('/telegram/auth', function (Request $request) {
    auth()->loginUsingId(UserBot::query()->where('chat_id', $request->input('id'))->first()->user?->id);
});

Route::get('/', HomeController::class)->name('home');
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

Route::fallback(function () {
    return Inertia::render('404');
});
