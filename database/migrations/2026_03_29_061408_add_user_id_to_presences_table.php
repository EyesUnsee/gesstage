<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            // Vérifier si la colonne candidat_id existe et la renommer
            if (Schema::hasColumn('presences', 'candidat_id')) {
                $table->renameColumn('candidat_id', 'user_id');
            } elseif (!Schema::hasColumn('presences', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('presences', 'heure_arrivee')) {
                $table->time('heure_arrivee')->nullable();
            }
            
            if (!Schema::hasColumn('presences', 'heure_depart')) {
                $table->time('heure_depart')->nullable();
            }
            
            if (!Schema::hasColumn('presences', 'heures_travaillees')) {
                $table->decimal('heures_travaillees', 5, 2)->default(0);
            }
            
            if (!Schema::hasColumn('presences', 'est_present')) {
                $table->boolean('est_present')->default(true);
            }
            
            if (!Schema::hasColumn('presences', 'est_justifie')) {
                $table->boolean('est_justifie')->default(false);
            }
            
            if (!Schema::hasColumn('presences', 'motif_absence')) {
                $table->text('motif_absence')->nullable();
            }
            
            // Ajouter un index unique
            if (!Schema::hasIndex('presences', ['user_id', 'stage_id', 'date'])) {
                $table->unique(['user_id', 'stage_id', 'date']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn([
                'heure_arrivee', 
                'heure_depart', 
                'heures_travaillees', 
                'est_present', 
                'est_justifie', 
                'motif_absence'
            ]);
            
            if (Schema::hasColumn('presences', 'user_id')) {
                $table->renameColumn('user_id', 'candidat_id');
            }
        });
    }
};
