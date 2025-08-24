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
        Schema::create('rappels_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('seance_id')->nullable()->constrained('seances')->onDelete('set null');
            $table->date('date_prevue');
            $table->time('heure_prevue');
            $table->boolean('confirme')->default(false);
            $table->boolean('rappel_envoye')->default(false);
            $table->enum('statut', ['en_attente', 'confirme', 'annule', 'termine'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index(['date_prevue', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rappels_rendez_vous');
    }
};
