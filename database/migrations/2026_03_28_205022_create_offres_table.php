<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->nullable()->constrained()->onDelete('set null');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('lieu')->nullable();
            $table->string('type')->default('stage');
            $table->integer('duree')->nullable(); // en mois
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('statut')->default('active'); // active, fermee, pourvue
            $table->text('competences')->nullable();
            $table->text('conditions')->nullable();
            $table->decimal('gratification', 10, 2)->nullable();
            $table->integer('places')->default(1);
            $table->timestamp('date_limite')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
