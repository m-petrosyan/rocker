<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Profile\BandController;
use App\Http\Controllers\Profile\BlogController;
use App\Http\Controllers\Profile\DashboardController;
use App\Http\Controllers\Profile\EventController;
use App\Http\Controllers\Profile\EventRequestController;
use App\Http\Controllers\Profile\GalleryController;
use App\Http\Controllers\Profile\MediaController;
use App\Http\Controllers\Profile\MergeProfilesController;
use App\Http\Controllers\Profile\NotificationController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\BlockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'email.verified.if.present', 'role:admin|moderator|organizer', 'activity'])
    ->as('profile.')->prefix('profile')->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('event/requests', [EventRequestController::class, 'index'])->name('events.requests');
        Route::get('event/{event}/requests', [EventRequestController::class, 'show'])->name('event.requests');
        Route::put('event/{event}/requests', [EventRequestController::class, 'update'])->name('event.request.update');
    });


Route::middleware(['auth', 'email.verified.if.present', 'activity'])->as('profile.')->prefix('profile')->group(
    function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('settings', [ProfileController::class, 'edit'])->name('edit');
        Route::put('settings', [ProfileController::class, 'update'])->name('update');
        Route::resource('events', EventController::class)->except('show');
        Route::resource('galleries', GalleryController::class)->except('show');
        Route::resource('bands', BandController::class)->except('show');
        Route::delete('bands/album/{album}/delete', [BandController::class, 'albumDestroy'])->name('album.delete');
        Route::resource('blogs', BlogController::class)->except('show');
        Route::post('image', [ProfileController::class, 'updateImage'])->name('media.update');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
        Route::post('merge/code', [MergeProfilesController::class, 'getCode'])->name('merge.code');
        Route::post('merge', [MergeProfilesController::class, 'mergeBot'])->name('merge.bot');
        Route::post('user/{user}/block', [BlockController::class, 'store'])->name('user.block');

        // Notifications routes
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    }
);

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


Route::post('/telegram/auth', [AuthenticatedSessionController::class, 'tgWebAuth']);
