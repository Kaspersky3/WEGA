<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyticsFilterRequest;
use App\Models\Product;
use App\Services\AnalyticsService;
use App\Services\ExportService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private ExportService $exportService
    ) {
    }

    public function dashboard(Request $request)
    {
        $categories = Product::query()
            ->select('categorie')
            ->distinct()
            ->orderBy('categorie')
            ->pluck('categorie')
            ->filter()
            ->values();

        $filters = $request->only(['categorie', 'period', 'limit', 'start_date', 'end_date']);

        return view('analytics.dashboard', [
            'categories' => $categories,
            'filters' => array_filter($filters),
        ]);
    }

    public function data(AnalyticsFilterRequest $request)
    {
        $filters = $request->validated();

        return response()->json([
            'top_selling' => $this->analyticsService->getTopSelling($filters)->map(function ($row) {
                return [
                    'product' => $row->product?->libelle,
                    'code' => $row->product?->code_produit,
                    'categorie' => $row->product?->categorie,
                    'quantity' => (int) $row->total_qty,
                    'revenue' => (float) $row->total_revenue,
                ];
            }),
            'top_profitable' => $this->analyticsService->getTopProfitable($filters)->map(function ($row) {
                return [
                    'product' => $row->product_label,
                    'code' => $row->product_code,
                    'categorie' => $row->product_category,
                    'quantity' => (int) $row->total_qty,
                    'revenue' => (float) $row->total_revenue,
                    'profit' => (float) $row->profit_value,
                ];
            }),
        ]);
    }

    public function export(AnalyticsFilterRequest $request, string $type, string $format)
    {
        $filters = $request->validated();
        $format = strtolower($format);

        $header = ['Produit', 'Code', 'Catégorie', 'Quantité', 'Montant', 'Bénéfice'];
        $rows = collect();

        if ($type === 'top-selling') {
            $header[5] = 'Chiffre d\'affaires';
            $rows = $this->analyticsService->getTopSelling($filters)->map(function ($row) {
                return [
                    $row->product?->libelle,
                    $row->product?->code_produit,
                    $row->product?->categorie,
                    (int) $row->total_qty,
                    (float) $row->total_revenue,
                    '',
                ];
            });
        } elseif ($type === 'top-profitable') {
            $rows = $this->analyticsService->getTopProfitable($filters)->map(function ($row) {
                return [
                    $row->product_label,
                    $row->product_code,
                    $row->product_category,
                    (int) $row->total_qty,
                    (float) $row->total_revenue,
                    (float) $row->profit_value,
                ];
            });
        } else {
            abort(404);
        }

        $filename = sprintf('%s-%s.%s', $type, now()->format('Ymd_His'), $format);

        return $this->exportService->download($header, $rows, $filename, $format);
    }
}

