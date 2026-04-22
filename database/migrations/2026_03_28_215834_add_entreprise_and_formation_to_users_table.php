<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'entreprise')) {
                $table->string('entreprise')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'formation')) {
                $table->string('formation')->nullable()->after('entreprise');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['entreprise', 'formation']);
        });
    }
};
