<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(protected TMDBService $tmdb) {}

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'Query is required'], 400);
        }

        $results = $this->tmdb->searchMovie($query);

        return response()->json($results);
    }

    public function show($id)
    {
        $movie = $this->tmdb->getMovieDetails($id);

        return response()->json($movie);
    }
}
