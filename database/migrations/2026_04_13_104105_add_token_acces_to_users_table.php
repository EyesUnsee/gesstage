<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('semaines_validees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('semaine');
            $table->integer('annee');
            $table->timestamp('validee_le')->nullable();
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['user_id', 'semaine', 'annee']);
            
            // Index pour les recherches
            $table->index(['user_id', 'annee', 'semaine']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('semaines_validees');
    }
};
