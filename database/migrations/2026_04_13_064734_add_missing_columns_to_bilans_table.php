<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bilans', function (Blueprint $table) {
            // Ajouter date_soumission si elle n'existe pas
            if (!Schema::hasColumn('bilans', 'date_soumission')) {
                $table->datetime('date_soumission')->nullable()->after('statut');
            }
            
            // Ajouter date_validation si elle n'existe pas
            if (!Schema::hasColumn('bilans', 'date_validation')) {
                $table->datetime('date_validation')->nullable()->after('date_soumission');
            }
            
            // Ajouter valide_par si elle n'existe pas
            if (!Schema::hasColumn('bilans', 'valide_par')) {
                $table->foreignId('valide_par')->nullable()->after('date_validation')->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('bilans', function (Blueprint $table) {
            if (Schema::hasColumn('bilans', 'date_soumission')) {
                $table->dropColumn('date_soumission');
            }
            if (Schema::hasColumn('bilans', 'date_validation')) {
                $table->dropColumn('date_validation');
            }
            if (Schema::hasColumn('bilans', 'valide_par')) {
                $table->dropForeign(['valide_par']);
                $table->dropColumn('valide_par');
            }
        });
    }
};
