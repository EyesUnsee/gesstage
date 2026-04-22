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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stage_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('evaluateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->decimal('note', 3, 1)->nullable(); // Note sur 5
            $table->text('commentaire')->nullable();
            $table->string('evaluateur')->nullable(); // Nom de l'évaluateur
            $table->string('evaluateur_nom')->nullable();
            $table->date('date_evaluation')->nullable();
            $table->enum('statut', ['en_attente', 'publie', 'archive'])->default('en_attente');
            $table->json('criteria')->nullable(); // Stocker les critères d'évaluation
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour optimiser les recherches
            $table->index(['candidat_id', 'statut']);
            $table->index('date_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
