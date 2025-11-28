<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $prixAchat = $this->faker->numberBetween(500, 1500);

        return [
            'categorie' => $this->faker->randomElement(['Vin & Boissons', 'Savons & Autres']),
            'libelle' => $this->faker->unique()->words(3, true),
            'prix_achat' => $prixAchat,
            'prix_vente' => $prixAchat + 500,
            'stock_initial' => 0,
            'stock_actuel' => 0,
            'lieu' => 'Stock',
            'type_produit' => 'Les deux',
            'stock_gros' => 10,
            'stock_detail' => 20,
            'stock_boutique_gros' => 0,
            'stock_boutique_detail' => 0,
            'prix_achat_gros' => $prixAchat * 12,
            'prix_vente_gros' => ($prixAchat + 400) * 12,
            'prix_achat_detail' => $prixAchat,
            'prix_vente_detail' => $prixAchat + 200,
            'bulk_unit_label' => 'Carton',
            'detail_unit_label' => 'Pièce',
            'units_per_bulk' => 12,
        ];
    }
}





