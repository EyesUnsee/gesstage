<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemainesValideesTable extends Migration
{
    public function up()
    {
        Schema::create('semaines_validees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('semaine');
            $table->integer('annee');
            $table->timestamp('validee_le');
            $table->timestamps();
            
            $table->unique(['user_id', 'semaine', 'annee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('semaines_validees');
    }
}
