<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query || mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $venues = Venue::query()
            ->where('location', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function (Venue $venue) {
                $cordinates = $venue->cordinates;
                $label = $venue->name ?: $venue->location;

                return [
                    'name' => $label,
                    'title' => $label,
                    'address' => $venue->location,
                    'location' => $venue->location,
                    'latitude' => $cordinates['latitude'] ?? null,
                    'longitude' => $cordinates['longitude'] ?? null,
                    'cid' => $venue->cid,
                ];
            });

        return response()->json($venues);
    }
}
