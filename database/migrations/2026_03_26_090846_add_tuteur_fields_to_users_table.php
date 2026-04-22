<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Colonnes pour les tuteurs
            if (!Schema::hasColumn('users', 'departement')) {
                $table->string('departement')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'poste')) {
                $table->string('poste')->nullable()->after('departement');
            }
            if (!Schema::hasColumn('users', 'universite')) {
                $table->string('universite')->nullable()->after('poste');
            }
            if (!Schema::hasColumn('users', 'bureau')) {
                $table->string('bureau')->nullable()->after('universite');
            }
            if (!Schema::hasColumn('users', 'max_stagiaires')) {
                $table->integer('max_stagiaires')->default(8)->after('bureau');
            }
            if (!Schema::hasColumn('users', 'experience')) {
                $table->string('experience')->nullable()->after('max_stagiaires');
            }
            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('experience');
            }
            if (!Schema::hasColumn('users', 'disponibilites')) {
                $table->text('disponibilites')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('users', 'expertises')) {
                $table->json('expertises')->nullable()->after('disponibilites');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'departement',
                'poste',
                'universite',
                'bureau',
                'max_stagiaires',
                'experience',
                'linkedin',
                'disponibilites',
                'expertises'
            ]);
        });
    }
};
