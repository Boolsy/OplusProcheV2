<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'pseudo' => $this->faker->userName, // Remplace 'name' par 'pseudo'
            'password' => Hash::make('password'), // Mot de passe par défaut
            'email' => $this->faker->unique()->safeEmail,
            'birth_date' => $this->faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'vip_status' => $this->faker->boolean ? 1 : 0,
            'banned' => 0,
            'role_id' => $this->faker->numberBetween(1, 2),
            'experience' => $this->faker->numberBetween(0, 1500),
            'level' => $this->faker->numberBetween(1, 20),
            'profile_image' => 'default-profile.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
