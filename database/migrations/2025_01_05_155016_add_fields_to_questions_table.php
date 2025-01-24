<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('corrected_by')->nullable()->after('updated_at');
            $table->boolean('deleted')->default(0)->after('corrected_by');

            // Ajoutez une clé étrangère si nécessaire
            $table->foreign('corrected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['corrected_by']);
            $table->dropColumn(['corrected_by', 'deleted']);
        });
    }
};
