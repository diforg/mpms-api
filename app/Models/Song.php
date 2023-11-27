<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'artist',
        'album',
        'year',
        'track_number',
        'gender',
        'length',
        'private_song',
        'path',
        'system_name',
    ];
    
}
