<?php

use App\Http\Controllers\api\ImageController;

Route::post('upload-image', [ImageController::class, 'store'])->name('upload-image')->middleware('auth');
