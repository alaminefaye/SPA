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
        Schema::table('seance_prestation', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà avant de les ajouter
            if (!Schema::hasColumn('seance_prestation', 'seance_id')) {
                $table->unsignedBigInteger('seance_id')->after('id');
                $table->foreign('seance_id')->references('id')->on('seances')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('seance_prestation', 'prestation_id')) {
                $table->unsignedBigInteger('prestation_id')->after('seance_id');
                $table->foreign('prestation_id')->references('id')->on('prestations')->onDelete('cascade');
            }
            
            // Vérifier si l'index n'existe pas déjà
            if (!$this->hasIndex('seance_prestation', ['seance_id', 'prestation_id'])) {
                $table->unique(['seance_id', 'prestation_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire, car les colonnes sont gérées par la migration de création de table
        // Cette migration est juste une sauvegarde au cas où la structure de la table serait incomplète
    }
    
    /**
     * Vérifie si un index existe déjà sur la table
     */
    protected function hasIndex($table, $columns)
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        
        $index = $table.'_'.implode('_', $columns).'_unique';
        return $doctrineTable->hasIndex($index);
    }
};
