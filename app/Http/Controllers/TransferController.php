<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferStoreRequest;
use App\Models\Product;
use App\Models\Transfer;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TransferController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'lieu_source', 'lieu_destination', 'from', 'to']);

        $query = Transfer::with(['product', 'user'])->orderByDesc('date_transfert');

        if ($search = Arr::get($filters, 'search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('libelle', 'like', "%{$search}%")
                            ->orWhere('code_produit', 'like', "%{$search}%");
                    });
            });
        }

        if ($lieuSource = Arr::get($filters, 'lieu_source')) {
            $query->where('lieu_source', $lieuSource);
        }

        if ($lieuDestination = Arr::get($filters, 'lieu_destination')) {
            $query->where('lieu_destination', $lieuDestination);
        }

        if (Arr::get($filters, 'from')) {
            $query->whereDate('date_transfert', '>=', $filters['from']);
        }

        if (Arr::get($filters, 'to')) {
            $query->whereDate('date_transfert', '<=', $filters['to']);
        }

        $transfers = $query->paginate(15)->withQueryString();

        return view('transfers.index', compact('transfers', 'filters'));
    }

    public function create(Request $request)
    {
        $productId = $request->get('product_id');
        $product = $productId ? Product::find($productId) : null;

        return view('transfers.create', compact('product'));
    }

    public function store(TransferStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $dateTransfert = Carbon::parse($data['date_transfert'])->startOfDay();

            $transfer = $this->transferService->storeTransfer(
                $request->user(),
                $data['product_id'],
                $data['lieu_source'],
                $data['lieu_destination'],
                (int) ($data['quantite_gros'] ?? 0),
                (int) ($data['quantite_detail'] ?? 0),
                $dateTransfert,
                Arr::get($data, 'notes')
            );

            return redirect()
                ->route('transfers.index')
                ->with('success', 'Transfert effectué avec succès.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['product', 'user']);

        return view('transfers.show', compact('transfer'));
    }
}
