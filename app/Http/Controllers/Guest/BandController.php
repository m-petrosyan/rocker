<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Band;
use App\Models\Genre;
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
            'genres' => Genre::whereHas('bands', function ($query) {
                $query->whereNotNull('user_id')->whereNotNull('info');
            })->orderBy('name')->get(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Band $band): Response
    {
        views($band)->record();

        return Inertia::render('Band/Band', [
            'band' => BandRepository::getBand($band),
        ]);
    }
}
