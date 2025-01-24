<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class GameController extends Controller
{
    /**
     * Récupère le classement des joueurs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaderboard()
    {
        // Test si la méthode est atteinte


        // Récupérer les 10 meilleurs joueurs classés par expérience
        $players = User::select('pseudo', 'experience as points')
            ->orderBy('experience', 'desc')
            ->take(10)
            ->get();



        return response()->json(['players' => $players]);
    }

}
