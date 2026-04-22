<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('validations', function (Blueprint $table) {
            // Ajouter la colonne urgent si elle n'existe pas
            if (!Schema::hasColumn('validations', 'urgent')) {
                $table->boolean('urgent')->default(false)->after('statut');
            }
            
            // Ajouter la colonne valide_par si elle n'existe pas
            if (!Schema::hasColumn('validations', 'valide_par')) {
                $table->foreignId('valide_par')->nullable()->after('urgent')->constrained('users')->onDelete('set null');
            }
            
            // Ajouter la colonne motif_rejet si elle n'existe pas
            if (!Schema::hasColumn('validations', 'motif_rejet')) {
                $table->text('motif_rejet')->nullable()->after('valide_par');
            }
            
            // Ajouter la colonne date_reponse si elle n'existe pas
            if (!Schema::hasColumn('validations', 'date_reponse')) {
                $table->dateTime('date_reponse')->nullable()->after('motif_rejet');
            }
            
            // Ajouter la colonne deleted_at (soft delete) si elle n'existe pas
            if (!Schema::hasColumn('validations', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down()
    {
        Schema::table('validations', function (Blueprint $table) {
            $columns = ['urgent', 'valide_par', 'motif_rejet', 'date_reponse', 'deleted_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('validations', $column)) {
                    if ($column === 'valide_par') {
                        $table->dropForeign(['valide_par']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
