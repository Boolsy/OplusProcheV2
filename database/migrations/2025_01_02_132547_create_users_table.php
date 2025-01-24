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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Spécifier le moteur
            $table->id();
            $table->string('pseudo', 50)->nullable(false);
            $table->string('password');
            $table->string('email', 100)->unique();
            $table->date('birth_date')->nullable();
            $table->boolean('vip_status')->default(false);
            $table->foreignId('role_id')->default(1)->constrained()->onDelete('cascade');
            $table->integer('experience')->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
