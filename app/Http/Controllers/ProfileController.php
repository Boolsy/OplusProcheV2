<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        $stats = DB::table('stats_game')
            ->where('user_id', $user->id)
            ->first();



        return response()->json([
            'user' => [
                'id' => $user->id,
                'pseudo' => $user->pseudo,
                'email' => $user->email,
                'role' => $user->role->name ?? 'Non défini',
                'birth_date' => $user->birth_date,
                'vip_status' => $user->vip_status ? 'Oui' : 'Non',
                'experience' => $user->experience,
                'level' => $user->level,
                'created_at' => $user->created_at->format('Y-m-d'),
                'profile_image' => $user->profile_image
                    ? ("{$user->profile_image}")
                    : url('images/default-profile.png'),
            ],
            'stats' => $stats ? [
                'games_played' => $stats->games_played,
                'games_won' => $stats->games_won,
                'correct_answers' => $stats->correct_answers,
                'last_game_date' => $stats->last_game_date,
            ] : [
                'games_played' => 0,
                'games_won' => 0,
                'correct_answers' => 0,
                'last_game_date' => null,
            ],

        ]);
    }

    public function validatePassword(Request $request)
    {
        $user = Auth::user();

        // Vérifie si l'ancien mot de passe correspond
        if (Hash::check($request->input('old_password'), $user->password)) {
            return response()->json(['valid' => true], 200);
        }

        return response()->json(['valid' => false, 'message' => 'Mot de passe incorrect'], 400);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'birth_date' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048', // Limite à 2 Mo
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed', // Assure que la confirmation est valide
            'new_password_confirmation' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Vérification des mots de passe
        if ($request->filled('old_password') || $request->filled('new_password')) {
            if (!Hash::check($request->input('old_password'), $user->password)) {
                return response()->json(['message' => 'L\'ancien mot de passe est incorrect.'], 400);
            }

            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->input('new_password'));
            }
            \Log::info($request->all());
        }

        // Mise à jour des champs basiques
        $user->pseudo = $validatedData['pseudo'];
        $user->email = $validatedData['email'];
        $user->birth_date = $validatedData['birth_date'] ?? $user->birth_date;

        // Gestion de l'upload d'image
        if ($request->hasFile('profile_image')) {
            // Supprime l'ancienne image si elle existe
            if ($user->profile_image && Storage::exists("public/{$user->profile_image}")) {
                Storage::delete("public/{$user->profile_image}");
            }

            // Enregistre la nouvelle image
            $path = $request->file('profile_image')->store("avatars/{$user->id}", 'public');
            $user->profile_image = $path; // Stocke le chemin dans la base de données

        }

        $user->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès !',
            'user' => [
                'id' => $user->id,
                'pseudo' => $user->pseudo,
                'email' => $user->email,
                'birth_date' => $user->birth_date,
                'profile_image' => $user->profile_image,
                'role' => $user->role->name ?? 'Non défini',
                'vip_status' => $user->vip_status ? 'Oui' : 'Non',
                'created_at' => $user->created_at->format('Y-m-d'),
                'level' => $user->level,
                'experience' => $user->experience,
            ],
        ]);
    }
}
