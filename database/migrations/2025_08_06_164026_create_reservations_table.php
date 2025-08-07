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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('salon_id')->constrained();
            $table->foreignId('prestation_id')->constrained();
            $table->decimal('prix', 10, 2);
            $table->time('duree');
            $table->dateTime('date_heure')->comment('Date et heure du rendez-vous');
            $table->enum('statut', ['en_attente', 'confirme', 'en_cours', 'termine', 'annule'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->boolean('client_created')->default(false)->comment('Indique si la réservation a été créée par un client');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
