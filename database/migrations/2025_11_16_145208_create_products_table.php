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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('code_produit')->unique(); // ID produit du type P001, P002...
        $table->string('categorie');
        $table->string('libelle');
        $table->decimal('prix_achat', 10, 2);
        $table->decimal('prix_vente', 10, 2);
        $table->unsignedInteger('stock_initial')->default(0);
        $table->unsignedInteger('stock_actuel')->default(0);
        $table->enum('lieu', ['Stock', 'Boutique'])->default('Stock');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
