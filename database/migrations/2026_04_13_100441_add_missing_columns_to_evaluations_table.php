<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Ajouter la colonne stagiaire_id si elle n'existe pas
            if (!Schema::hasColumn('evaluations', 'stagiaire_id')) {
                $table->foreignId('stagiaire_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            
            // Ajouter la colonne evaluateur_id si elle n'existe pas
            if (!Schema::hasColumn('evaluations', 'evaluateur_id')) {
                $table->foreignId('evaluateur_id')->nullable()->after('stagiaire_id')->constrained('users')->onDelete('cascade');
            }
            
            // Ajouter les colonnes de notes si elles n'existent pas
            if (!Schema::hasColumn('evaluations', 'competences_techniques')) {
                $table->integer('competences_techniques')->nullable()->after('evaluateur_id');
            }
            
            if (!Schema::hasColumn('evaluations', 'qualite_travail')) {
                $table->integer('qualite_travail')->nullable()->after('competences_techniques');
            }
            
            if (!Schema::hasColumn('evaluations', 'respect_delais')) {
                $table->integer('respect_delais')->nullable()->after('qualite_travail');
            }
            
            if (!Schema::hasColumn('evaluations', 'communication')) {
                $table->integer('communication')->nullable()->after('respect_delais');
            }
            
            if (!Schema::hasColumn('evaluations', 'autonomie')) {
                $table->integer('autonomie')->nullable()->after('communication');
            }
            
            if (!Schema::hasColumn('evaluations', 'esprit_equipe')) {
                $table->integer('esprit_equipe')->nullable()->after('autonomie');
            }
            
            if (!Schema::hasColumn('evaluations', 'commentaires')) {
                $table->text('commentaires')->nullable()->after('esprit_equipe');
            }
            
            if (!Schema::hasColumn('evaluations', 'note')) {
                $table->integer('note')->nullable()->after('commentaires');
            }
        });
    }

    public function down()
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $columns = ['stagiaire_id', 'evaluateur_id', 'competences_techniques', 'qualite_travail', 
                        'respect_delais', 'communication', 'autonomie', 'esprit_equipe', 'commentaires', 'note'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('evaluations', $column)) {
                    if ($column === 'stagiaire_id' || $column === 'evaluateur_id') {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
