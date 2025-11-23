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
        if (Schema::hasTable('inventory_lines') && ! Schema::hasTable('inventory_details')) {
            Schema::rename('inventory_lines', 'inventory_details');
        }

        if (! Schema::hasTable('inventory_details')) {
            return;
        }

        Schema::table('inventory_details', function (Blueprint $table) {
            $table->unsignedInteger('stock_theorique_gros')->default(0)->after('product_id');
            $table->unsignedInteger('stock_theorique_detail')->default(0)->after('stock_theorique_gros');
            $table->unsignedBigInteger('stock_theorique_total')->default(0)->after('stock_theorique_detail');
            $table->unsignedBigInteger('stock_reel_total')->default(0)->after('stock_reel_detail');
            $table->unsignedInteger('conversion_rate')->default(1)->after('stock_reel_total');
            $table->bigInteger('gap_units')->default(0)->after('conversion_rate');
            $table->decimal('gap_value', 14, 2)->default(0)->after('gap_units');
            $table->decimal('unit_purchase_price', 12, 2)->default(0)->after('gap_value');

            $table->dropColumn([
                'stock_initial_gros',
                'stock_initial_detail',
                'stock_final_theorique_gros',
                'stock_final_theorique_detail',
                'quantite_vendue_gros',
                'quantite_vendue_detail',
                'ca_gros',
                'ca_detail',
                'ca_total',
                'cost_gros',
                'cost_detail',
                'cost_total',
                'margin',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('inventory_details')) {
            return;
        }

        Schema::table('inventory_details', function (Blueprint $table) {
            $table->unsignedInteger('stock_initial_gros')->default(0)->after('stock_theorique_total');
            $table->unsignedInteger('stock_initial_detail')->default(0)->after('stock_initial_gros');
            $table->unsignedInteger('stock_final_theorique_gros')->default(0)->after('stock_initial_detail');
            $table->unsignedInteger('stock_final_theorique_detail')->default(0)->after('stock_final_theorique_gros');
            $table->unsignedInteger('quantite_vendue_gros')->default(0)->after('stock_final_theorique_detail');
            $table->unsignedInteger('quantite_vendue_detail')->default(0)->after('quantite_vendue_gros');
            $table->decimal('ca_gros', 12, 2)->default(0)->after('quantite_vendue_detail');
            $table->decimal('ca_detail', 12, 2)->default(0)->after('ca_gros');
            $table->decimal('ca_total', 12, 2)->default(0)->after('ca_detail');
            $table->decimal('cost_gros', 12, 2)->default(0)->after('ca_total');
            $table->decimal('cost_detail', 12, 2)->default(0)->after('cost_gros');
            $table->decimal('cost_total', 12, 2)->default(0)->after('cost_detail');
            $table->decimal('margin', 12, 2)->default(0)->after('cost_total');

            $table->dropColumn([
                'stock_theorique_gros',
                'stock_theorique_detail',
                'stock_theorique_total',
                'stock_reel_total',
                'conversion_rate',
                'gap_units',
                'gap_value',
                'unit_purchase_price',
            ]);
        });

        if (Schema::hasTable('inventory_details')) {
            Schema::rename('inventory_details', 'inventory_lines');
        }
    }
};

