<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_validate_inventory_and_calculations_are_correct(): void
    {
        $user = User::factory()->manager()->create();

        $product = Product::factory()->create([
            'stock_gros' => 10,
            'stock_detail' => 5,
            'units_per_bulk' => 12,
            'prix_achat_detail' => 1000,
            'prix_achat_gros' => 9000,
        ]);

        $payload = [
            'inventory_date' => now()->toDateString(),
            'products' => [
                [
                    'product_id' => $product->id,
                    'stock_reel_gros' => 8,
                    'stock_reel_detail' => 10,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('inventories.store'), $payload);
        $response->assertRedirect();

        $inventory = Inventory::with('details')->first();
        $this->assertNotNull($inventory);

        $detail = $inventory->details->first();
        $this->assertInstanceOf(InventoryDetail::class, $detail);

        $this->assertEquals(10, $detail->stock_theorique_gros);
        $this->assertEquals(5, $detail->stock_theorique_detail);
        $this->assertEquals((10 * 12) + 5, $detail->stock_theorique_total);
        $this->assertEquals((8 * 12) + 10, $detail->stock_reel_total);
        $this->assertEquals(19, $detail->gap_units);

        $product->refresh();
        $this->assertEquals(8, $product->stock_gros);
        $this->assertEquals(10, $product->stock_detail);
        $this->assertEquals((8 * 12) + 10, $product->stock_actuel);
    }
}

