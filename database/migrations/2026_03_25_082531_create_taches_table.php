<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la table existe déjà
        if (!Schema::hasTable('taches')) {
            Schema::create('taches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('titre');
                $table->text('description')->nullable();
                $table->enum('priorite', ['high', 'medium', 'low'])->default('medium');
                $table->string('categorie')->default('tache');
                $table->date('echeance')->nullable();
                $table->boolean('terminee')->default(false);
                $table->timestamp('date_fin')->nullable();
                $table->boolean('cree_par_tuteur')->default(false);
                $table->timestamps();
                
                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
                
                $table->index('user_id');
                $table->index(['user_id', 'terminee']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
