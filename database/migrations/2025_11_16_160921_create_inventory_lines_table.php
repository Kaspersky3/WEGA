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
        Schema::create('inventory_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Stocks réels comptés
            $table->unsignedInteger('stock_reel_gros')->default(0);
            $table->unsignedInteger('stock_reel_detail')->default(0);
            
            // Stocks théoriques au début du mois
            $table->unsignedInteger('stock_initial_gros')->default(0);
            $table->unsignedInteger('stock_initial_detail')->default(0);
            
            // Stocks théoriques à la fin du mois (avant inventaire)
            $table->unsignedInteger('stock_final_theorique_gros')->default(0);
            $table->unsignedInteger('stock_final_theorique_detail')->default(0);
            
            // Quantités vendues calculées
            $table->unsignedInteger('quantite_vendue_gros')->default(0);
            $table->unsignedInteger('quantite_vendue_detail')->default(0);
            
            // Montants calculés
            $table->decimal('ca_gros', 12, 2)->default(0); // CA pour ventes en gros
            $table->decimal('ca_detail', 12, 2)->default(0); // CA pour ventes en détail
            $table->decimal('ca_total', 12, 2)->default(0); // CA total
            $table->decimal('cost_gros', 12, 2)->default(0); // Coût pour ventes en gros
            $table->decimal('cost_detail', 12, 2)->default(0); // Coût pour ventes en détail
            $table->decimal('cost_total', 12, 2)->default(0); // Coût total
            $table->decimal('margin', 12, 2)->default(0); // Marge (CA - Coût)
            
            $table->timestamps();
            
            $table->index(['inventory_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_lines');
    }
};
