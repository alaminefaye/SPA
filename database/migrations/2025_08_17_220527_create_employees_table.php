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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('numero_telephone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('adresse')->nullable();
            $table->date('date_naissance')->nullable();
            $table->date('date_embauche');
            $table->string('poste');
            $table->text('specialites')->nullable();
            $table->decimal('salaire', 10, 2)->nullable();
            $table->foreignId('salon_id')->nullable()->constrained()->onDelete('set null');
            $table->string('photo')->nullable();
            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
