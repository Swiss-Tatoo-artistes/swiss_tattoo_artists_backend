<?php

namespace App\Http\Controllers;

use App\Models\TattooArtist;
use Illuminate\Http\Request;


class TattooArtistController extends Controller
{

    private function validateTattooArtist(Request $request)
    {
        // Validation of datas
        $request->validate([
            'presentation' => 'string',
            'home_phone' => '',
            'phone' => 'required',
            'facebook_sociallink' => '',
            'instagram_sociallink' => '',
            'x_sociallink' => '',
            'tiktok_sociallink' => '',
        ]);
    }

    //Display all the tattooartists
    public function index()
    {
        $tattooArtists = TattooArtist::with(['user' => function ($query) {
            $query
                ->where('is_tattoo_artist', true)
                ->select('id', 'name', 'lastname', 'pseudo');
        }])
            ->get();

        return response()->json(['tattooArtists' => $tattooArtists], 200);
    }


    // Create new tattoo artist
    public function create(Request $request)
    {

        $this->validateTattooArtist($request);

        $newTattooArtist = new TattooArtist();
        $newTattooArtist->fill($request->all());
        $newTattooArtist->save();

        // Check if the creation is done
        if ($newTattooArtist) {
            return response()->json(['message' => 'Tattoo artist created successfully'], 201);
        } else {
            return response()->json(['message' => 'Failed to create tattoo artist'], 500);
        }
    }


    // Update a specific tattoo artist
    public function update(Request $request, $id)
    {
        $this->validateTattooArtist($request);

        $updateTattooArtist = TattooArtist::find($id);
        if (!$updateTattooArtist) {
            return response()->json(['message' => 'Tattoo artist not found'], 404);
        }

        $updateTattooArtist->fill($request->all());
        $updateTattooArtist->save();

        return response()->json(['message' => 'Tattoo artist updated successfully'], 200);
    }


    // Delete a specific tattoo artist
    public function delete($id)
    {
        $deleted = TattooArtist::destroy($id);

        if ($deleted) {
            return response()->json(['message' => 'Tattoo artist deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete tattoo artist'], 404);
        }
    }


    // Display a specific tattoo artists
    public function show($id)
    {

        $tattooArtist = TattooArtist::where('id', $id)
            ->with(['user' => function ($query) {
                $query
                    ->select('id', 'name', 'lastname', 'pseudo');
            }])
            ->first();

        if ($tattooArtist) {
            return response()->json(['tattooArtist' => $tattooArtist], 200);
        } else {
            return response()->json(['message' => 'Tattoo artist not found'], 404);
        }
    }


    // Get the addresses of a specific tattoo artist
    public function getAdresses($id)
    {
        $tattooArtist = TattooArtist::with('adresses.canton')->find($id);

        if (!$tattooArtist) {
            return response()->json(['error' => 'Tattoo artist not found'], 404);
        }

        return response()->json(['adresses' => $tattooArtist->adresses], 200);
    }


    public function getAdressesAndOpeningtimes($id)
    {
        $tattooArtist = TattooArtist::with(['adresses.canton', 'openingTimes'])->find($id);

        if (!$tattooArtist) {
            return response()->json(['error' => 'Tattoo artist not found'], 404);
        }

        return response()->json([
            'adresses' => $tattooArtist->adresses,
            'openingTimes' => $tattooArtist->openingTimes
        ], 200);
    }
}
