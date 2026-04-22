<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluateur_id')->constrained('users')->onDelete('cascade');
            $table->integer('competences_techniques')->nullable();
            $table->integer('qualite_travail')->nullable();
            $table->integer('respect_delais')->nullable();
            $table->integer('communication')->nullable();
            $table->integer('autonomie')->nullable();
            $table->integer('esprit_equipe')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};
