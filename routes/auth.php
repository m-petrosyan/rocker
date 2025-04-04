<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Profile\EventController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->prefix('profile')->group(function () {
    Route::get('/{username}', [\App\Http\Controllers\Profile\ProfileController::class, 'index'])->name(
        'profile.index'
    );
    Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('events', EventController::class)->except('show');
});

Route::middleware('auth')->group(function () {
    Route::get('verification', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('email/verify', [EmailVerificationNotificationController::class, 'verify'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
