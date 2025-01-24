<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatsGame extends Model
{
    use HasFactory;

    protected $table = 'stats_game';
    protected $fillable = [
        'user_id',           // Lien avec l'utilisateur
        'games_played',      // Nombre de parties jouées
        'games_won',         // Nombre de parties gagnées
        'correct_answers',   // Nombre de bonnes réponses
        'last_game_date',    // Date de la dernière partie
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
