<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
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
        views($band)->record();

        return Inertia::render('Band/Band', [
            'band' => BandRepository::getBand($band),
        ]);
    }
}
