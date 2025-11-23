<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type_vente' => $this->faker->randomElement(['Gros', 'Détail']),
            'quantite' => $this->faker->numberBetween(1, 50),
            'prix_unitaire' => $this->faker->numberBetween(500, 2500),
            'montant_total' => fn (array $attributes) => $attributes['quantite'] * $attributes['prix_unitaire'],
            'date_vente' => $this->faker->date(),
            'notes' => null,
        ];
    }
}



