<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la table existe avant de la créer
        if (!Schema::hasTable('pointages')) {
            Schema::create('pointages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('date');
                $table->time('heure_arrivee')->nullable();
                $table->time('heure_depart')->nullable();
                $table->enum('statut', ['present', 'absent', 'retard', 'justifie'])->default('present');
                $table->text('justification')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
                
                $table->index(['user_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
