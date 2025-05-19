<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Profile\BandController;
use App\Http\Controllers\Profile\BlogController;
use App\Http\Controllers\Profile\EventController;
use App\Http\Controllers\Profile\GalleryController;
use App\Http\Controllers\Profile\MediaController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->as('profile.')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('settings', [ProfileController::class, 'edit'])->name('edit');
    Route::put('settings', [ProfileController::class, 'update'])->name('update');
    Route::resource('events', EventController::class)->except('show');
    Route::resource('galleries', GalleryController::class)->except('show');
    Route::resource('bands', BandController::class)->except('show');
    Route::resource('blogs', BlogController::class)->except('show');
    Route::post('image', [ProfileController::class, 'updateImage'])->name('media.update');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->name('verification.send');

    Route::post('email/verify', [EmailVerificationNotificationController::class, 'verify'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
