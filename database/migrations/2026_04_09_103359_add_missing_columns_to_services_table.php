<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Ajouter la colonne statut si elle n'existe pas
            if (!Schema::hasColumn('services', 'statut')) {
                $table->enum('statut', ['actif', 'inactif', 'en-attente'])->default('actif')->after('description');
            }
            
            // Ajouter la colonne tags si elle n'existe pas
            if (!Schema::hasColumn('services', 'tags')) {
                $table->string('tags')->nullable()->after('statut');
            }
            
            // Ajouter d'autres colonnes si nécessaire
            if (!Schema::hasColumn('services', 'code')) {
                $table->string('code')->unique()->nullable()->after('nom');
            }
            
            if (!Schema::hasColumn('services', 'email')) {
                $table->string('email')->nullable()->after('responsable_id');
            }
            
            if (!Schema::hasColumn('services', 'telephone')) {
                $table->string('telephone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('services', 'adresse')) {
                $table->string('adresse')->nullable()->after('telephone');
            }
            
            if (!Schema::hasColumn('services', 'logo')) {
                $table->string('logo')->nullable()->after('adresse');
            }
            
            if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('logo');
            }
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['statut', 'tags', 'code', 'email', 'telephone', 'adresse', 'logo', 'is_active']);
        });
    }
};
