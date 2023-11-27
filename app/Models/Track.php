<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'song_id',
        'playlist_id',
        'name',
        'track_number',
        'description',
        'year',
        'sensibility',
        'tag',
    ];
    
}
