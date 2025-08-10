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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('telephone');
            $table->string('email');
            $table->foreignId('salon_id')->nullable()->constrained('salons')->onDelete('set null');
            $table->string('numero_ticket')->nullable();
            $table->string('prestation')->nullable();
            $table->string('sujet');
            $table->string('photo')->nullable();
            $table->text('message');
            $table->boolean('is_priority')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
