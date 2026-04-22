<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('heure_arrivee')->nullable();
            $table->time('heure_depart')->nullable();
            $table->decimal('heures_travaillees', 5, 2)->default(0);
            $table->boolean('est_present')->default(true);
            $table->boolean('est_justifie')->default(false);
            $table->text('motif_absence')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('user_id');
            $table->index('stage_id');
            $table->index('date');
            $table->unique(['user_id', 'stage_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
