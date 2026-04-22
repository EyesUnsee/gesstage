<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntreprisesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('entreprises')) {
            Schema::create('entreprises', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('siret')->unique()->nullable();
                $table->string('code_naf')->nullable();
                $table->string('forme_juridique')->nullable();
                $table->string('email')->unique();
                $table->string('telephone')->nullable();
                $table->string('fax')->nullable();
                $table->string('site_web')->nullable();
                $table->string('adresse');
                $table->string('code_postal');
                $table->string('ville');
                $table->string('pays')->default('France');
                $table->string('secteur_activite')->nullable();
                $table->integer('nombre_salaries')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('entreprises');
    }
}
