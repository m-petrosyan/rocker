<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Repositories\BandRepository;
use Inertia\Inertia;
use Inertia\Response;

class BandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Band/Bands', [
            'bands' => BandRepository::bandList(),
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Band $band): Response
    {
        $band->load([
            'genres',
            'galleries.user' => fn($q) => $q->select('id', 'user_id', 'title')->limit(5),
            'links',
            'albums' => fn($q) => $q->select('id', 'band_id', 'title', 'cover')->limit(10),
            'events' => fn($q) => $q->upcoming()->select('id', 'title', 'date', 'venue')->limit(5),
            'blogs' => fn($q) => $q->latest()->select('id', 'title', 'excerpt', 'slug')->limit(3),
        ]);

        views($band)->record();

        return Inertia::render('Band/Band', ['band' => $band]);
    }
}
