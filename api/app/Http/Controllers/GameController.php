<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        return Game::select('player_x', 'player_o', 'winner')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function store(Request $request)
    {
        return Game::create($request->all());
    }
}
