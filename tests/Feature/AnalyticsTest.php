<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_top_products(): void
    {
        $user = User::factory()->manager()->create();

        $productA = Product::factory()->create([
            'prix_achat_detail' => 800,
            'prix_achat_gros' => 8000,
        ]);

        $productB = Product::factory()->create([
            'prix_achat_detail' => 1200,
            'prix_achat_gros' => 12000,
        ]);

        Sale::create([
            'product_id' => $productA->id,
            'type_vente' => 'Détail',
            'quantite' => 20,
            'prix_unitaire' => 2000,
            'montant_total' => 40000,
            'date_vente' => now()->toDateString(),
        ]);

        Sale::create([
            'product_id' => $productB->id,
            'type_vente' => 'Détail',
            'quantite' => 10,
            'prix_unitaire' => 5000,
            'montant_total' => 50000,
            'date_vente' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->getJson(route('analytics.data', [
            'period' => 'month',
            'limit' => 5,
        ]));

        $response->assertOk()
            ->assertJsonStructure([
                'top_selling',
                'top_profitable',
            ]);

        $json = $response->json();
        $this->assertSame($productA->libelle, $json['top_selling'][0]['product']);
        $this->assertGreaterThanOrEqual(2, count($json['top_profitable']));
        $this->assertGreaterThan($json['top_profitable'][1]['profit'], $json['top_profitable'][0]['profit']);
    }
}

