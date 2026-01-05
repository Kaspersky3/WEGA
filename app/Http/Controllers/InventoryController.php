<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryStoreRequest;
use App\Http\Requests\InventoryImportRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\ExportService;
use App\Services\ImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private ExportService $exportService,
        private ImportService $importService
    )
    {
    }

    public function index(Request $request)
    {
        if ($request->user()) {
            $this->authorize('viewAny', Inventory::class);
        }

        $filters = $request->only(['search', 'status', 'lieu', 'from', 'to', 'sort', 'direction']);

        $sort = Arr::get($filters, 'sort', 'inventory_date');
        $direction = Arr::get($filters, 'direction', 'desc');

        $allowedSort = ['inventory_date', 'total_gap_units', 'total_gap_value', 'reference'];
        if (! in_array($sort, $allowedSort, true)) {
            $sort = 'inventory_date';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $query = Inventory::with('user')->orderBy($sort, $direction);

        if ($search = Arr::get($filters, 'search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('month_name', 'like', "%{$search}%");
            });
        }

        if ($status = Arr::get($filters, 'status')) {
            $query->where('status', $status);
        }

        if (Arr::get($filters, 'from')) {
            $query->whereDate('inventory_date', '>=', $filters['from']);
        }

        if (Arr::get($filters, 'to')) {
            $query->whereDate('inventory_date', '<=', $filters['to']);
        }

        $inventories = $query->paginate(15)->withQueryString();

        return view('inventories.index', compact('inventories', 'filters'));
    }

    public function create(Request $request)
    {
        if ($request->user()) {
            $this->authorize('create', Inventory::class);
        }

        $filters = $request->only(['categorie', 'search', 'reference', 'lieu']);
        
        // Le lieu est obligatoire pour créer un inventaire
        if (!Arr::get($filters, 'lieu')) {
            return view('inventories.create', [
                'products' => collect(),
                'filters' => $filters,
                'inventoryDate' => now()->toDateString(),
                'theoreticalTotal' => 0,
                'requireLieu' => true,
            ]);
        }

        $products = $this->inventoryService->getProductsSnapshot($filters);

        $rows = $products->values()->map(fn (Product $product) => $this->presentProduct($product));
        $theoreticalTotal = $rows->sum('stock_theorique_total');

        return view('inventories.create', [
            'products' => $rows,
            'filters' => $filters,
            'inventoryDate' => now()->toDateString(),
            'theoreticalTotal' => $theoreticalTotal,
            'requireLieu' => false,
        ]);
    }

    public function store(InventoryStoreRequest $request)
    {
        $data = $request->validated();

        $inventoryDate = Carbon::parse($data['inventory_date'])->startOfDay();

        $inventory = $this->inventoryService->storeInventory(
            $request->user(),
            $inventoryDate,
            array_values($data['products']),
            Arr::get($data, 'notes'),
            Arr::get($data, 'filters', []),
            Arr::get($data, 'lieu')
        );

        return redirect()
            ->route('inventories.show', $inventory)
            ->with('success', 'Inventaire validé avec succès.');
    }

    public function show(Inventory $inventory)
    {
        if (request()->user()) {
            $this->authorize('view', $inventory);
        }

        $inventory->load(['details.product', 'user']);

        return view('inventories.show', compact('inventory'));
    }

    public function historyExport(Request $request)
    {
        if ($request->user()) {
            $this->authorize('export', Inventory::class);
        }

        $format = $request->get('format', 'xlsx');
        $filters = $request->only(['search', 'status', 'lieu', 'from', 'to']);

        $query = Inventory::with('user')->orderByDesc('inventory_date');

        if ($search = Arr::get($filters, 'search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('month_name', 'like', "%{$search}%");
            });
        }

        if ($lieu = Arr::get($filters, 'lieu')) {
            $query->where('lieu', $lieu);
        }

        if ($status = Arr::get($filters, 'status')) {
            $query->where('status', $status);
        }

        if (Arr::get($filters, 'from')) {
            $query->whereDate('inventory_date', '>=', $filters['from']);
        }

        if (Arr::get($filters, 'to')) {
            $query->whereDate('inventory_date', '<=', $filters['to']);
        }

        $inventories = $query->get();

        $header = [
            'Référence',
            'Date',
            'Lieu',
            'Utilisateur',
            'Stock théorique',
            'Stock réel',
            'Écart (unités)',
            'Valorisation (FCFA)',
        ];

        $rows = $inventories->map(function (Inventory $inventory) {
            return [
                $inventory->reference,
                optional($inventory->inventory_date)->format('d/m/Y H:i'),
                $inventory->lieu ?? 'N/A',
                $inventory->user?->name,
                $inventory->total_stock_theorique,
                $inventory->total_stock_reel,
                $inventory->total_gap_units,
                $inventory->total_gap_value,
            ];
        });

        $filename = sprintf('inventaires-%s.%s', now()->format('Ymd_His'), $format);

        return $this->exportService->download($header, $rows, $filename, $format);
    }

    public function detailsExport(Inventory $inventory, string $format)
    {
        if (request()->user()) {
            $this->authorize('export', $inventory);
        }

        $inventory->load('details.product', 'user');

        $header = [
            'Produit',
            'Code',
            'Stock théorique (gros)',
            'Stock théorique (détail)',
            'Stock réel (gros)',
            'Stock réel (détail)',
            'Total théorique',
            'Total réel',
            'Écart (unités)',
            'Valorisation (FCFA)',
        ];

        $rows = $inventory->details->map(function ($detail) {
            return [
                $detail->product->libelle,
                $detail->product->code_produit,
                $detail->stock_theorique_gros,
                $detail->stock_theorique_detail,
                $detail->stock_reel_gros,
                $detail->stock_reel_detail,
                $detail->stock_theorique_total,
                $detail->stock_reel_total,
                $detail->gap_units,
                $detail->gap_value,
            ];
        });

        $filename = sprintf('inventaire-%s.%s', $inventory->reference, $format);

        return $this->exportService->download($header, $rows, $filename, $format);
    }

    public function import(InventoryImportRequest $request)
    {
        if ($request->user()) {
            $this->authorize('create', Inventory::class);
        }

        $file = $request->file('csv_file');
        $inventoryDate = Carbon::parse($request->inventory_date)->startOfDay();
        $notes = $request->notes;
        $lieu = $request->lieu;

        if (!$lieu) {
            return redirect()
                ->route('inventories.create')
                ->withErrors(['lieu' => 'Le lieu est obligatoire pour créer un inventaire.'])
                ->withInput();
        }

        // Parser le fichier CSV
        $result = $this->importService->parseInventoryFile($file);

        // Si des erreurs critiques, retourner avec les erreurs
        if (!empty($result['errors']) && empty($result['products'])) {
            return redirect()
                ->route('inventories.create')
                ->withErrors(['csv_file' => 'Erreurs lors de l\'import : ' . implode(', ', $result['errors'])])
                ->withInput();
        }

        // Si aucun produit valide, retourner une erreur
        if (empty($result['products'])) {
            return redirect()
                ->route('inventories.create')
                ->withErrors(['csv_file' => 'Aucun produit valide trouvé dans le fichier.'])
                ->withInput();
        }

        // Créer l'inventaire
        $inventory = $this->inventoryService->storeInventory(
            $request->user(),
            $inventoryDate,
            $result['products'],
            $notes,
            [],
            $lieu
        );

        $message = 'Inventaire importé avec succès.';
        if (!empty($result['errors'])) {
            $message .= ' ' . count($result['errors']) . ' erreur(s) rencontrée(s) (voir les logs).';
        }

        return redirect()
            ->route('inventories.show', $inventory)
            ->with('success', $message)
            ->with('import_errors', $result['errors'] ?? []);
    }

    protected function presentProduct(Product $product): array
    {
        $conversionRate = $product->conversionRate();
        $stockTheoGros = (int) ($product->stock_gros ?? 0);
        $stockTheoDetail = (int) ($product->stock_detail ?? 0);
        $stockTheoTotal = ($stockTheoGros * $conversionRate) + $stockTheoDetail;

        return [
            'product' => $product,
            'conversion_rate' => $conversionRate,
            'stock_theorique_gros' => $stockTheoGros,
            'stock_theorique_detail' => $stockTheoDetail,
            'stock_theorique_total' => $stockTheoTotal,
            'bulk_unit_label' => $product->bulk_unit_label,
            'detail_unit_label' => $product->detail_unit_label,
            'unit_purchase_price' => $product->unitPurchasePrice('detail') ?: 0,
        ];
    }
}
