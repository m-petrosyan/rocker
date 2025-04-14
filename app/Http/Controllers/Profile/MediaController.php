<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function __invoke(Media $media): RedirectResponse
    {
        $media->delete();

        return redirect()->back()->withInput();
    }
}
