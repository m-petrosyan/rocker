<?php

use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'index'])->name('home');

Route::resource('events', EventController::class)->only('index', 'show');

Route::resource('galleries', GalleryController::class)->only('index', 'show');

Route::get('verification', EmailVerificationPromptController::class)
    ->name('verification.notice');
Route::get('profile/{username}', [ProfileController::class, 'index'])->name(
    'profile.show'
);

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

require_once __DIR__.'/guest.php';
require_once __DIR__.'/auth.php';
