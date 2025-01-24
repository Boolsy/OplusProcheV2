<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les champs pouvant être remplis via un formulaire ou un tableau.
     * @var array<string>
     */
    protected $fillable = [
        'pseudo',
        'email',
        'password',
        'experience',
        'level',
        'vip_status',
        'birth_date',
        'profile_image',
        'banned',
        'role_id',
    ];

    /**
     * Les attributs cachés pour les tableaux de sérialisation.
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à caster dans des types natifs.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'vip_status' => 'boolean',
        'birth_date' => 'date',
        'experience' => 'integer',
        'banned' => 'boolean',
    ];

    /**
     * Relation avec le modèle `Role` : chaque utilisateur appartient à un rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class)->withDefault([
            'name' => 'Non défini',
        ]);
    }

    /**
     * Relation avec le modèle `StatsGame` : chaque utilisateur a une statistique de jeu.
     */
    public function stats()
    {
        return $this->hasOne(StatsGame::class, 'user_id');
    }


    /**
     * Accesseur pour l'URL de l'image de profil.
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/default-profile.png');
    }

    /**
     * Relation avec le modèle `QuestionsSubmitted` : un utilisateur peut soumettre plusieurs questions.
     */
    public function submittedQuestions()
    {
        return $this->hasMany(QuestionsSubmitted::class);
    }

    /**
     * Attribue un rôle par défaut lors de la création d'un utilisateur.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!isset($user->role_id)) { // Vérifie uniquement si `role_id` n'est pas défini
                $user->role_id = Role::where('name', 'Joueur')->value('id');
            }
        });
    }
}
