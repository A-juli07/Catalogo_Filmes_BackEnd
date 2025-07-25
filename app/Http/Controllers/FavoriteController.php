<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Movie;
use App\Services\TMDBService;

class FavoriteController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Listar filmes favoritos do usuário",
     *     tags={"Favoritos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Filtrar por gênero",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de filmes favoritos"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $genreFilter = $request->query('genre');

        $favorites = auth()->user()->favorites()->with('movie')->get();

        if ($favorites->isEmpty()) {
            return response()->json([
                'message' => 'Você ainda não tem filmes favoritos.'
            ], 200);
        }

        if ($genreFilter) {
            $favorites = $favorites->filter(function ($fav) use ($genreFilter) {
                if (!$fav->movie) return false;

                $genres = is_array($fav->movie->genre)
                    ? $fav->movie->genre
                    : json_decode($fav->movie->genre, true);

                foreach ($genres as $genre) {
                    if (isset($genre['name']) && strcasecmp($genre['name'], $genreFilter) === 0) {
                        return true;
                    }
                }
                return false;
            })->values();

            if ($favorites->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum filme favorito encontrado para o gênero "' . $genreFilter . '"'
                ], 200);
            }
        }

        $result = $favorites->map(function ($fav) {
            $movie = $fav->movie;
            return [
                'tmdb_id' => $movie->tmdb_id,
                'title' => $movie->title,
                'overview' => $movie->overview,
                'poster_path' => $movie->poster_path,
                'release_date' => $movie->release_date,
                'genre' => json_encode($movie->genre),
            ];
        });

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/api/favorites",
     *     summary="Adicionar um filme aos favoritos",
     *     tags={"Favoritos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"movie_id"},
     *             @OA\Property(property="movie_id", type="integer", example=900667)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Filme adicionado aos favoritos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filme não encontrado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|integer',
        ]);

        $movieId = $request->input('movie_id');

        // Verifica se já está favoritado
        $existingFavorite = auth()->user()->favorites()
            ->whereHas('movie', function($query) use ($movieId) {
                $query->where('tmdb_id', $movieId);
            })->first();

        if ($existingFavorite) {
            return response()->json([
                'message' => 'Este filme já está na sua lista de favoritos!'
            ], 200);
        }

        $movie = Movie::where('tmdb_id', $movieId)->first();

        if (!$movie) {
            $movieData = $this->tmdb->getMovieDetails($movieId);

            if (!$movieData || isset($movieData['success']) && !$movieData['success']) {
                return response()->json([
                    'message' => 'Desculpe, não conseguimos encontrar este filme no TMDB.'
                ], 404);
            }

            $movie = Movie::create([
                'tmdb_id' => $movieData['id'],
                'title' => $movieData['title'],
                'overview' => $movieData['overview'],
                'poster_path' => $movieData['poster_path'],
                'release_date' => $movieData['release_date'],
                'genre' => json_encode($movieData['genres']),
            ]);
        }

        $favorite = Favorite::create([
            'user_id' => auth()->id(),
            'movie_id' => $movie->id,
        ]);

        return response()->json([
            'message' => 'Filme adicionado aos favoritos com sucesso!',
            'movie' => $movie->title,
            'favorite' => $favorite
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/favorites/{movie_id}",
     *     summary="Remover um filme dos favoritos",
     *     tags={"Favoritos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="movie_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filme removido dos favoritos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filme não encontrado nos favoritos"
     *     )
     * )
     */
    public function delete($tmdb_id)
    {
        $movie = Movie::where('tmdb_id', $tmdb_id)->first();

        if (!$movie) {
            return response()->json([
                'message' => 'Filme não encontrado na nossa base de dados.'
            ], 404);
        }

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('movie_id', $movie->id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'message' => 'Este filme não está na sua lista de favoritos.'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'message' => "O filme '{$movie->title}' foi removido dos seus favoritos com sucesso.",
            'action' => 'removed'
        ]);
    }

    public function genres()
    {
        $favorites = auth()->user()->favorites()->with('movie')->get();

        if ($favorites->isEmpty()) {
            return response()->json([
                'message' => 'Você ainda não tem filmes favoritos para listar gêneros.'
            ], 200);
        }

        $allGenres = [];

        foreach ($favorites as $fav) {
            $genres = json_decode($fav->movie->genre ?? '[]', true);
            foreach ($genres as $genre) {
                $allGenres[$genre['id']] = $genre['name'];
            }
        }

        $uniqueGenres = [];

        foreach ($allGenres as $id => $name) {
            $uniqueGenres[] = [
                'id' => $id,
                'name' => $name
            ];
        }

        if (empty($uniqueGenres)) {
            return response()->json([
                'message' => 'Nenhum gênero encontrado para seus filmes favoritos.'
            ], 200);
        }

        return response()->json([
            'message' => 'Gêneros dos seus filmes favoritos:',
            'genres' => $uniqueGenres
        ]);
    }
}