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
        Schema::table('seances', function (Blueprint $table) {
            // Suppression de la contrainte de clé étrangère avant de supprimer la colonne
            $table->dropForeign(['prestation_id']);
            $table->dropColumn('prestation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seances', function (Blueprint $table) {
            $table->unsignedBigInteger('prestation_id')->nullable();
            $table->foreign('prestation_id')->references('id')->on('prestations');
        });
    }
};
