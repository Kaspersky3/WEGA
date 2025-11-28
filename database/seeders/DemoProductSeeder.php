<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()
            ->count(10)
            ->state(function () {
                $type = fake()->randomElement(['Gros', 'Détail', 'Les deux']);
                $bulk = $type !== 'Détail';
                $detail = $type !== 'Gros';

                return array_merge([
                    'type_produit' => $type,
                    'lieu' => fake()->randomElement(['Stock', 'Boutique']),
                    'categorie' => fake()->randomElement([
                        'Vin & Boissons',
                        'Produits Alimentaires',
                        'Savons & Autres',
                        'Biscuits & BonBon',
                    ]),
                ], $bulk ? [
                    'stock_gros' => fake()->numberBetween(5, 50),
                    'prix_achat_gros' => fake()->numberBetween(20000, 60000) / 100,
                    'prix_vente_gros' => fake()->numberBetween(60000, 120000) / 100,
                    'bulk_unit_label' => fake()->randomElement(['Carton', 'Palette', 'Boîte']),
                    'units_per_bulk' => fake()->numberBetween(6, 36),
                ] : [
                    'stock_gros' => 0,
                    'prix_achat_gros' => null,
                    'prix_vente_gros' => null,
                    'bulk_unit_label' => 'Carton',
                    'units_per_bulk' => 1,
                ], $detail ? [
                    'stock_detail' => fake()->numberBetween(20, 200),
                    'prix_achat_detail' => fake()->numberBetween(500, 2000),
                    'prix_vente_detail' => fake()->numberBetween(2000, 5000),
                    'detail_unit_label' => fake()->randomElement(['Pièce', 'Bouteille', 'Paquet']),
                ] : [
                    'stock_detail' => 0,
                    'prix_achat_detail' => null,
                    'prix_vente_detail' => null,
                    'detail_unit_label' => 'Unité',
                ]);
            })
            ->create();
    }
}





