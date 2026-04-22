<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
            $table->enum('type', ['avertissement', 'suspension', 'exclusion', 'retenue'])->default('avertissement');
            $table->text('motif');
            $table->enum('gravite', ['faible', 'moyenne', 'elevee'])->default('moyenne');
            $table->string('duree')->nullable();
            $table->enum('statut', ['actif', 'termine', 'en_attente', 'en_appel'])->default('actif');
            $table->foreignId('cree_par')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanctions');
    }
};
