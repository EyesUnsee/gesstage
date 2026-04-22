<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->string('titre')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bilans', function (Blueprint $table) {
            $table->string('titre')->nullable(false)->change();
        });
    }
};
