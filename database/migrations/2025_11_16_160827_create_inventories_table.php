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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->year('year'); // Année de l'inventaire
            $table->unsignedTinyInteger('month'); // Mois (1-12)
            $table->string('month_name'); // Nom du mois (ex: "Janvier 2025")
            $table->decimal('total_ca', 12, 2)->default(0); // Chiffre d'affaires total du mois
            $table->decimal('total_cost', 12, 2)->default(0); // Coût total des achats
            $table->decimal('total_margin', 12, 2)->default(0); // Marge totale
            $table->text('notes')->nullable(); // Notes optionnelles
            $table->timestamps();
            
            // Index pour éviter les doublons de mois
            $table->unique(['year', 'month']);
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
