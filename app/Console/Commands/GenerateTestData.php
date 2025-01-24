<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GenerateTestData extends Command
{
    protected $signature = 'generate:test-data';
    protected $description = 'Génère des données de test avec un utilisateur admin et des utilisateurs fictifs';

    public function handle()
    {
        try {
            // Réinitialisation des tables
            DB::table('stats_game')->delete();
            DB::table('users')->delete();

            $this->info('Tables réinitialisées.');

            // Réinitialisation des auto-incréments
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE stats_game AUTO_INCREMENT = 1');

            $this->info('Auto-incréments réinitialisés.');

            // Création de l'utilisateur admin
            $this->info('Création de l\'utilisateur admin...');
            $adminEmail = $this->ask('Entrez l\'email de l\'admin');
            $adminPassword = $this->secret('Entrez un mot de passe pour l\'admin');

            $admin = User::create([
                'pseudo' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'birth_date' => now()->subYears(25),
                'vip_status' => 1,
                'level' => 45,
                'experience' => 150,
                'profile_image' => 'default-profile.png',
                'created_at' => now(),
                'updated_at' => now(),
                'role_id' => 3,
            ]);
            $this->info('Admin créé avec succès.');

            // Ajouter des statistiques pour l'admin
            DB::table('stats_game')->insert([
                'user_id' => $admin->id,
                'games_played' => rand(20, 50),
                'games_won' => rand(10, 25),
                'correct_answers' => rand(50, 100),
                'last_game_date' => now()->subDays(rand(1, 7)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info('Statistiques de l\'admin ajoutées.');

            // Création des utilisateurs fictifs
            $this->info('Création des utilisateurs fictifs...');
            \App\Models\User::factory(20)->create()->each(function ($user) {
                DB::table('stats_game')->insert([
                    'user_id' => $user->id,
                    'games_played' => rand(0, 50),
                    'games_won' => rand(0, 25),
                    'correct_answers' => rand(0, 100),
                    'last_game_date' => now()->subDays(rand(0, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            $this->info('20 utilisateurs fictifs avec leurs statistiques ont été créés.');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Une erreur est survenue : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
