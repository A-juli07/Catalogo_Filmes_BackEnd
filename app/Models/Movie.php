<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Favorite;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'poster_path',
        'release_date',
        'genre',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
