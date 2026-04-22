<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('documents');
        
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titre');
            $table->string('type');
            $table->string('fichier_path');
            $table->string('fichier_nom');
            $table->decimal('taille', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('statut')->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); // Ajoute la colonne deleted_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
