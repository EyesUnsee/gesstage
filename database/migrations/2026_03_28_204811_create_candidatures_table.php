<?php
// database/migrations/2024_01_01_000001_create_candidatures_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('users')->onDelete('cascade');
            $table->string('titre');
            $table->string('entreprise')->nullable();
            $table->string('type')->default('developpement');
            $table->enum('statut', ['en_attente', 'en_cours', 'acceptee', 'refusee'])->default('en_attente');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->date('date_reponse')->nullable();
            $table->text('description')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('lettre_motivation_path')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index('statut');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidatures');
    }
};
