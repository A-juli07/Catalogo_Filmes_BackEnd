<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function __construct(protected TMDBService $tmdb) {}


    /**
     * @OA\Get(
     *     path="/api/movies/search",
     *     summary="Buscar filmes pelo nome",
     *     tags={"Filmes"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Nome do filme",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de filmes encontrados"
     *     )
     * )
     */

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['error' => 'É preciso o nome do filme'], 400);
        }

        $results = $this->tmdb->searchMovie($query);

        $filtered = collect($results['results'] ?? [])->map(function ($movie) {
            return [
                'id' => $movie['id'] ?? null,
                'title' => $movie['title'] ?? null,
                'overview' => $movie['overview'] ?? null,
                'release_date' => $movie['release_date'] ?? null,
                'vote_average' => $movie['vote_average'] ?? null,
                'poster_path' => $movie['poster_path'] ?? null,
            ];
        });

        return response()->json([
            'results' => $filtered,
            'total_results' => $results['total_results'] ?? count($filtered),
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/movies/{id}",
     *     summary="Buscar detalhes de um filme",
     *     tags={"Filmes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do filme no TMDB",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do filme"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filme não encontrado"
     *     )
     * )
     */

    public function show($id)
    {
        $movie = $this->tmdb->getMovieDetails($id);

        if (!$movie || isset($movie['status_code'])) {
            return response()->json(['error' => 'Filme não encontrado'], 404);
        }

        $filtered = [
            'title' => $movie['title'] ?? null,
            'overview' => $movie['overview'] ?? null,
            'release_date' => $movie['release_date'] ?? null,
            'runtime' => $movie['runtime'] ?? null,
            'vote_average' => $movie['vote_average'] ?? null,
            'genres' => $movie['genres'] ?? [],
            'poster_path' => $movie['poster_path'] ?? null,
        ];

        return response()->json($filtered);
    }
}
