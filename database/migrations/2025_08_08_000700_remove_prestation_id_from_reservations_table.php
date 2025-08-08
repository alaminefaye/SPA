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
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign(['prestation_id']);
            // Supprimer la colonne
            $table->dropColumn('prestation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Recréer la colonne
            $table->foreignId('prestation_id')->nullable()->constrained();
        });
    }
};
