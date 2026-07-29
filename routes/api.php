<?php

use App\Http\Controllers\api\ImageController;
use App\Http\Controllers\api\VenueController;

Route::post('upload-image', [ImageController::class, 'store'])->name('upload-image')->middleware('auth');
Route::get('venues/search', [VenueController::class, 'search'])->name('venues.search');
