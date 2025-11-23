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
        Schema::table('products', function (Blueprint $table) {
            // Type de produit
            $table->enum('type_produit', ['Gros', 'Détail', 'Les deux'])->default('Détail')->after('lieu');
            
            // Stocks Gros et Détail
            $table->unsignedInteger('stock_gros')->default(0)->after('type_produit');
            $table->unsignedInteger('stock_detail')->default(0)->after('stock_gros');
            
            // Stocks Boutique (pour plus tard)
            $table->unsignedInteger('stock_boutique_gros')->default(0)->after('stock_detail');
            $table->unsignedInteger('stock_boutique_detail')->default(0)->after('stock_boutique_gros');
            
            // Prix Gros
            $table->decimal('prix_achat_gros', 10, 2)->nullable()->after('stock_boutique_detail');
            $table->decimal('prix_vente_gros', 10, 2)->nullable()->after('prix_achat_gros');
            
            // Prix Détail (renommer les anciens champs ou garder pour compatibilité)
            $table->decimal('prix_achat_detail', 10, 2)->nullable()->after('prix_vente_gros');
            $table->decimal('prix_vente_detail', 10, 2)->nullable()->after('prix_achat_detail');
            
            // Statistiques de ventes
            $table->unsignedInteger('quantite_vendue_gros')->default(0)->after('prix_vente_detail');
            $table->unsignedInteger('quantite_vendue_detail')->default(0)->after('quantite_vendue_gros');
            $table->decimal('montant_total_ventes', 12, 2)->default(0)->after('quantite_vendue_detail');
            $table->decimal('montant_total_achats', 12, 2)->default(0)->after('montant_total_ventes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'type_produit',
                'stock_gros',
                'stock_detail',
                'stock_boutique_gros',
                'stock_boutique_detail',
                'prix_achat_gros',
                'prix_vente_gros',
                'prix_achat_detail',
                'prix_vente_detail',
                'quantite_vendue_gros',
                'quantite_vendue_detail',
                'montant_total_ventes',
                'montant_total_achats',
            ]);
        });
    }
};
