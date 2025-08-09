<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Met à jour toutes les séances existantes pour avoir date_seance = aujourd'hui
     */
    public function up(): void
    {
        // Mettre à jour toutes les séances existantes pour avoir date_seance = aujourd'hui
        DB::table('seances')
            ->whereNull('date_seance')
            ->update(['date_seance' => now()->toDateString()]);
    }

    /**
     * Reverse the migrations.
     * Cette opération ne peut pas être annulée, donc on ne fait rien
     */
    public function down(): void
    {
        // On ne peut pas vraiment annuler cette opération
        // car on ne connaît pas les valeurs précédentes
    }
};
