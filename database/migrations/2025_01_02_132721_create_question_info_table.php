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
        Schema::create('question_info', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Spécifier le moteur
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('info_text');
            $table->string('image_url', 255)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_info');
    }
};
