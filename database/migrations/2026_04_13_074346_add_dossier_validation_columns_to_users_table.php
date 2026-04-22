<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'dossier_valide')) {
                $table->boolean('dossier_valide')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('users', 'date_validation_dossier')) {
                $table->timestamp('date_validation_dossier')->nullable()->after('dossier_valide');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dossier_valide', 'date_validation_dossier']);
        });
    }
};
