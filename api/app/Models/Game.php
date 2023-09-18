<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'player_x',
        'player_o',
        'winner',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
