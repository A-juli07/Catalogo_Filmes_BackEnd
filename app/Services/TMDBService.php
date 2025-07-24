<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected string $baseUrl = 'https://api.themoviedb.org/3';

    public function searchMovie(string $query)
    {
        $response = Http::withToken(env('TMDB_API_KEY'))
            ->get("{$this->baseUrl}/search/movie", [
                'query' => $query,
                'language' => 'pt-BR'
            ]);

        return $response->json();
    }

    public function getMovieDetails(int $id)
    {
        $response = Http::withToken(env('TMDB_API_KEY'))
            ->get("{$this->baseUrl}/movie/{$id}", [
                'language' => 'pt-BR'
            ]);

        return $response->json();
    }
}
