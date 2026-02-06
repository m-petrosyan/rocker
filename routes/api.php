<?php

use App\Http\Controllers\Dashboard\ImageController;

Route::post('upload-image', [ImageController::class, 'store'])->name('upload-image')->middleware('auth');
