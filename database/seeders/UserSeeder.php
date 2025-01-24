<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les rôles
        $roles = DB::table('roles')->pluck('id', 'name');

        // Insérer les utilisateurs
        DB::table('users')->insert([
            [
                'pseudo' => 'AdminUser',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['Admin'], // ID du rôle Admin
                'experience' => 0,
                'level' => 1,
                'vip_status' => false,
                'birth_date' => '1990-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pseudo' => 'Joueur1',
                'email' => 'joueur1@example.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['Joueur'], // ID du rôle Joueur
                'experience' => 10,
                'level' => 2,
                'vip_status' => false,
                'birth_date' => '1995-05-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pseudo' => 'SolidePlayer',
                'email' => 'joueursolide@example.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['Joueur Solide'], // ID du rôle Joueur Solide
                'experience' => 50,
                'level' => 5,
                'vip_status' => true,
                'birth_date' => '1993-07-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
