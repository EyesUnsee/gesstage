<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bilans', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'rejete', 'brouillon'])->default('brouillon');
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tuteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->decimal('note', 5, 2)->nullable();
            $table->text('commentaire_tuteur')->nullable();
            $table->text('commentaire_chef')->nullable();
            $table->string('fichier_path')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_validation')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['statut', 'stagiaire_id']);
            $table->index('date_debut');
            $table->index('date_fin');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bilans');
    }
};
