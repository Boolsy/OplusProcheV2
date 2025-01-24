<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getAllUsers() {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function updateUserRole(Request $request, $id)
    {
        // Récupère l'utilisateur cible dont on souhaite modifier le rôle
        $targetUser = User::findOrFail($id);

        // Vérifie si l'utilisateur cible est déjà un administrateur
        if ($targetUser->role_id === 3) {
            return response()->json(['error' => "Vous ne pouvez pas modifier le rôle d'un administrateur."], 403);
        }

        // Met à jour le rôle uniquement si la vérification passe
        $targetUser->role_id = $request->input('role_id');
        $targetUser->save();

        return response()->json(['message' => 'Rôle mis à jour avec succès']);
    }


    public function updateUserVIP(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->vip_status = $request->input('vip_status'); // Correction : input('vip_status')
        $user->save();

        return response()->json(['message' => 'Statut VIP mis à jour avec succès']);
    }

    public function toggleBanUser($id) {
        $user = User::findOrFail($id);
        $user->banned = !$user->banned;
        $user->save();

        return response()->json(['message' => 'Statut de bannissement modifié avec succès']);
    }


    public function deleteUser($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
