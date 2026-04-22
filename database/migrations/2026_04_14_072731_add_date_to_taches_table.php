<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Vérifier si la colonne date n'existe pas
        if (!Schema::hasColumn('taches', 'date')) {
            // Ajouter la colonne date comme nullable d'abord
            Schema::table('taches', function (Blueprint $table) {
                $table->date('date')->nullable()->after('description');
            });
            
            // Mettre à jour les enregistrements existants avec une date par défaut
            DB::table('taches')->whereNull('date')->update(['date' => now()->format('Y-m-d')]);
            
            // Rendre la colonne non nullable
            Schema::table('taches', function (Blueprint $table) {
                $table->date('date')->nullable(false)->change();
            });
        }
        
        // Ajouter la colonne priorite si elle n'existe pas
        if (!Schema::hasColumn('taches', 'priorite')) {
            Schema::table('taches', function (Blueprint $table) {
                $table->enum('priorite', ['low', 'medium', 'high'])->default('medium')->after('date');
            });
        }
        
        // Ajouter la colonne terminee si elle n'existe pas
        if (!Schema::hasColumn('taches', 'terminee')) {
            Schema::table('taches', function (Blueprint $table) {
                $table->boolean('terminee')->default(false)->after('priorite');
            });
        }
        
        // Ajouter soft deletes si elle n'existe pas
        if (!Schema::hasColumn('taches', 'deleted_at')) {
            Schema::table('taches', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }
    
    public function down()
    {
        Schema::table('taches', function (Blueprint $table) {
            $table->dropColumn(['date', 'priorite', 'terminee', 'deleted_at']);
        });
    }
};
