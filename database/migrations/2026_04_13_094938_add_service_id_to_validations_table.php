<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('validations', function (Blueprint $table) {
            // Ajouter la colonne service_id si elle n'existe pas
            if (!Schema::hasColumn('validations', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('user_id')->constrained('services')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('validations', function (Blueprint $table) {
            if (Schema::hasColumn('validations', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
        });
    }
};
