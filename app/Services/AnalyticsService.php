<?php

namespace App\Services;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    public function getTopSelling(array $filters = []): Collection
    {
        [$start, $end] = $this->resolvePeriod($filters);
        $limit = $this->limit($filters);

        $cacheKey = $this->cacheKey('top-selling', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'categorie' => Arr::get($filters, 'categorie'),
            'limit' => $limit,
        ]);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start, $end, $filters, $limit) {
            $query = Sale::query()
                ->selectRaw('product_id, SUM(quantite) as total_qty, SUM(montant_total) as total_revenue')
                ->whereBetween('date_vente', [$start->toDateString(), $end->toDateString()])
                ->groupBy('product_id')
                ->orderByDesc('total_qty')
                ->with(['product:id,libelle,code_produit,categorie']);

            if ($categorie = Arr::get($filters, 'categorie')) {
                $query->whereHas('product', function ($q) use ($categorie) {
                    $q->where('categorie', $categorie);
                });
            }

            return $query->take($limit)->get();
        });
    }

    public function getTopProfitable(array $filters = []): Collection
    {
        [$start, $end] = $this->resolvePeriod($filters);
        $limit = $this->limit($filters);

        $cacheKey = $this->cacheKey('top-profitable', [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'categorie' => Arr::get($filters, 'categorie'),
            'limit' => $limit,
        ]);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($start, $end, $filters, $limit) {
            $query = Sale::query()
                ->join('products', 'products.id', '=', 'sales.product_id')
                ->selectRaw('
                    sales.product_id,
                    products.libelle as product_label,
                    products.code_produit as product_code,
                    products.categorie as product_category,
                    SUM(sales.quantite) as total_qty,
                    SUM(sales.montant_total) as total_revenue,
                    SUM(
                        (sales.prix_unitaire - COALESCE(
                            CASE
                                WHEN sales.type_vente = "Gros" AND products.prix_achat_gros IS NOT NULL THEN products.prix_achat_gros
                                WHEN products.prix_achat_detail IS NOT NULL THEN products.prix_achat_detail
                                ELSE products.prix_achat
                            END, 0
                        )) * sales.quantite
                    ) as profit_value
                ')
                ->whereBetween('sales.date_vente', [$start->toDateString(), $end->toDateString()])
                ->groupBy('sales.product_id', 'products.libelle', 'products.code_produit', 'products.categorie')
                ->orderByDesc('profit_value');

            if ($categorie = Arr::get($filters, 'categorie')) {
                $query->where('products.categorie', $categorie);
            }

            return $query->take($limit)->get();
        });
    }

    private function resolvePeriod(array $filters): array
    {
        $period = Arr::get($filters, 'period', 'month');
        $today = Carbon::today();

        return match ($period) {
            'day' => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
            'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            'custom' => $this->customPeriod($filters, $today),
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        };
    }

    private function customPeriod(array $filters, Carbon $fallback): array
    {
        $start = Arr::get($filters, 'start_date');
        $end = Arr::get($filters, 'end_date');

        if ($start && $end) {
            return [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];
        }

        return [$fallback->copy()->startOfMonth(), $fallback->copy()->endOfMonth()];
    }

    private function limit(array $filters): int
    {
        $limit = (int) Arr::get($filters, 'limit', 5);
        return in_array($limit, [5, 10, 20], true) ? $limit : 5;
    }

    private function cacheKey(string $prefix, array $payload): string
    {
        return sprintf('analytics:%s:%s', $prefix, md5(json_encode($payload)));
    }
}

