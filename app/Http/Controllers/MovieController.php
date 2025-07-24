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

        return response()->json($results);
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

        return response()->json($movie);
    }
}
