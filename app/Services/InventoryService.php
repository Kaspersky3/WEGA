<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Prépare la liste des produits pour l'écran d'inventaire avec les stocks théoriques.
     */
    public function getProductsSnapshot(array $filters = []): Collection
    {
        $query = Product::query();

        $query->when(Arr::get($filters, 'categorie'), function ($q, $categorie) {
            $q->where('categorie', $categorie);
        });

        $query->when(Arr::get($filters, 'search'), function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('libelle', 'like', "%{$search}%")
                    ->orWhere('code_produit', 'like', "%{$search}%");
            });
        });

        $query->when(Arr::get($filters, 'reference'), function ($q, $reference) {
            $q->where('code_produit', 'like', "%{$reference}%");
        });

        return $query->orderBy('libelle')->get();
    }

    /**
     * Enregistre un inventaire complet.
     *
     * @param array<int, array<string, mixed>> $lines
     */
    public function storeInventory(?User $user, Carbon $inventoryDate, array $lines, ?string $notes = null, array $filters = []): Inventory
    {
        $productIds = collect($lines)->pluck('product_id')->unique()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return DB::transaction(function () use ($user, $inventoryDate, $lines, $products, $notes, $filters) {
            $inventory = Inventory::create([
                'reference' => $this->generateReference($inventoryDate),
                'user_id' => $user?->id,
                'inventory_date' => $inventoryDate,
                'status' => 'validated',
                'year' => $inventoryDate->year,
                'month' => $inventoryDate->month,
                'month_name' => ucfirst($inventoryDate->locale('fr')->isoFormat('MMMM YYYY')),
                'notes' => $notes,
                'filters_snapshot' => $filters,
            ]);

            $totals = [
                'stock_theorique' => 0,
                'stock_reel' => 0,
                'gap_units' => 0,
                'gap_value' => 0,
            ];

            foreach ($lines as $line) {
                $product = $products->get($line['product_id']);

                if (! $product) {
                    continue;
                }

                $conversionRate = $product->conversionRate();
                $stockTheoriqueGros = (int) ($product->stock_gros ?? 0);
                $stockTheoriqueDetail = (int) ($product->stock_detail ?? 0);
                $stockTheoriqueTotal = ($stockTheoriqueGros * $conversionRate) + $stockTheoriqueDetail;

                $stockReelGros = (int) Arr::get($line, 'stock_reel_gros', 0);
                $stockReelDetail = (int) Arr::get($line, 'stock_reel_detail', 0);
                $stockReelTotal = ($stockReelGros * $conversionRate) + $stockReelDetail;

                $gapUnits = $stockTheoriqueTotal - $stockReelTotal;

                $unitPurchasePrice = $product->unitPurchasePrice('detail');
                if (! $unitPurchasePrice && $product->prix_achat_gros && $conversionRate > 0) {
                    $unitPurchasePrice = ((float) $product->prix_achat_gros) / $conversionRate;
                }

                $gapValue = $gapUnits * $unitPurchasePrice;

                InventoryDetail::create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $product->id,
                    'stock_theorique_gros' => $stockTheoriqueGros,
                    'stock_theorique_detail' => $stockTheoriqueDetail,
                    'stock_theorique_total' => $stockTheoriqueTotal,
                    'stock_reel_gros' => $stockReelGros,
                    'stock_reel_detail' => $stockReelDetail,
                    'stock_reel_total' => $stockReelTotal,
                    'conversion_rate' => $conversionRate,
                    'gap_units' => $gapUnits,
                    'gap_value' => $gapValue,
                    'unit_purchase_price' => $unitPurchasePrice,
                ]);

                $product->update([
                    'stock_gros' => $stockReelGros,
                    'stock_detail' => $stockReelDetail,
                    'stock_actuel' => $stockReelTotal,
                ]);

                $totals['stock_theorique'] += $stockTheoriqueTotal;
                $totals['stock_reel'] += $stockReelTotal;
                $totals['gap_units'] += $gapUnits;
                $totals['gap_value'] += $gapValue;
            }

            $inventory->update([
                'total_stock_theorique' => $totals['stock_theorique'],
                'total_stock_reel' => $totals['stock_reel'],
                'total_gap_units' => $totals['gap_units'],
                'total_gap_value' => $totals['gap_value'],
                'total_ca' => 0,
                'total_cost' => 0,
                'total_margin' => 0,
            ]);

            return $inventory->load('details.product', 'user');
        });
    }

    protected function generateReference(Carbon $date): string
    {
        do {
            $reference = sprintf('INV-%s-%s', $date->format('Ymd'), Str::upper(Str::random(4)));
        } while (Inventory::where('reference', $reference)->exists());

        return $reference;
    }
}

