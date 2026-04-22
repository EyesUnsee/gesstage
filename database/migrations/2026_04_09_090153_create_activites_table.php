<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('icone')->nullable();
            $table->string('type')->default('info');
            $table->string('statut')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_nom')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->boolean('lu')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
            $table->index(['type', 'lu']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activites');
    }
};
