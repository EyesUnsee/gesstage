<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la colonne candidat_id existe, renommez-la
        if (Schema::hasColumn('documents', 'candidat_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->renameColumn('candidat_id', 'user_id');
            });
        }
        
        // Si la colonne n'existe pas du tout, ajoutez-la
        if (!Schema::hasColumn('documents', 'user_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('user_id', 'candidat_id');
        });
    }
};
