<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Image\ImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{

    public function store(ImageUploadRequest $imageUploadRequest): JsonResponse
    {
        $media = auth()->user()
            ->addMedia($imageUploadRequest['image'])
            ->toMediaCollection($imageUploadRequest['collection']);

        return response()->json(['url' => $media->getUrl()], 201);
    }

    public function destroy(Media $media): RedirectResponse
    {
        $media->delete();

        return redirect()->back()->withInput();
    }

}
