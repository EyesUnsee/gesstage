<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titre');
            $table->text('contenu');
            $table->date('date_journal')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->text('commentaire_tuteur')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'statut']);
            $table->index('date_journal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journaux');
    }
};
