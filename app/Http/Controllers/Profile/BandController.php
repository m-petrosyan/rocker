<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Band\BandCreateRequest;
use App\Models\Band;
use App\Repositories\BandRepository;
use App\Services\BandService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BandController extends Controller
{
    public function __construct(protected BandService $bandService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Profile/Bands/BandCreateEdit', [
            'bandsList' => BandRepository::bandList(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BandCreateRequest $request)
    {
        $this->bandService->store($request->validated());
//        dd($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Band $band)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Band $band)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Band $band)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Band $band)
    {
        // չենք ջնջում այլ մաքրումն ենք սաղ ինֆոն + նկարները բացի անունից
    }
}
