<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Profile\EventController;
use App\Http\Controllers\Profile\GalleryController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->as('profile.')->prefix('profile')->group(function () {
    Route::get('/', [\App\Http\Controllers\Profile\ProfileController::class, 'index'])->name('index');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/settings', [ProfileController::class, 'update'])->name('update');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('destroy');


    Route::resource('events', EventController::class)->except('show');
    Route::resource('gallery', GalleryController::class)->except('show');
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
