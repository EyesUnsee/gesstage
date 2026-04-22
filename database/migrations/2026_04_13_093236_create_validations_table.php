<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('validations')) {
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
                $table->string('titre');
                $table->string('type')->default('document');
                $table->text('description')->nullable();
                $table->string('fichier_path')->nullable();
                $table->string('fichier_nom')->nullable();
                $table->enum('statut', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
                $table->boolean('urgent')->default(false);
                $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
                $table->text('motif_rejet')->nullable();
                $table->dateTime('date_reponse')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Index pour optimiser les recherches
                $table->index('statut');
                $table->index('type');
                $table->index('service_id');
                $table->index('created_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('validations');
    }
};
