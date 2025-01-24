<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stats_game', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Spécifier le moteur
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('games_played')->default(0);
            $table->integer('games_won')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->timestamp('last_game_date')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats_game');
    }
};
