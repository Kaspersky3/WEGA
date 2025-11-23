<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire {{ $inventory->reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <p class="text-muted mb-1">Inventaire</p>
                <h1 class="h4 mb-0">{{ $inventory->reference }} · {{ $inventory->month_name }}</h1>
                <small class="text-muted">Réalisé le {{ $inventory->inventory_date?->format('d/m/Y H:i') }} par {{ $inventory->user?->name ?? 'N/A' }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'csv']) }}" class="btn btn-outline-secondary btn-sm">Exporter CSV</a>
                <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'xlsx']) }}" class="btn btn-outline-secondary btn-sm">Exporter Excel</a>
                <a href="{{ route('inventories.index') }}" class="btn btn-primary">Retour à l'historique</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Stock théorique</p>
                        <h3 class="mb-0">{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Stock réel</p>
                        <h3 class="mb-0">{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Écart total</p>
                        <h3 class="{{ $inventory->total_gap_units >= 0 ? 'text-danger' : 'text-success' }} mb-0">
                            {{ number_format($inventory->total_gap_units, 0, ',', ' ') }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Valorisation</p>
                        <h3 class="mb-0">{{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Stock théorique (gros / détail)</th>
                                <th>Stock réel (gros / détail)</th>
                                <th>Total théorique</th>
                                <th>Total réel</th>
                                <th>Écart (unités)</th>
                                <th>Valorisation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory->details as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $detail->product->libelle }}</div>
                                        <small class="text-muted">{{ $detail->product->code_produit }} · {{ $detail->product->categorie }}</small>
                                    </td>
                                    <td>
                                        <div>Gros : {{ number_format($detail->stock_theorique_gros, 0, ',', ' ') }}</div>
                                        <div>Détail : {{ number_format($detail->stock_theorique_detail, 0, ',', ' ') }}</div>
                                    </td>
                                    <td>
                                        <div>Gros : {{ number_format($detail->stock_reel_gros, 0, ',', ' ') }}</div>
                                        <div>Détail : {{ number_format($detail->stock_reel_detail, 0, ',', ' ') }}</div>
                                    </td>
                                    <td>{{ number_format($detail->stock_theorique_total, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($detail->stock_reel_total, 0, ',', ' ') }}</td>
                                    <td class="{{ $detail->gap_units >= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($detail->gap_units, 0, ',', ' ') }}
                                    </td>
                                    <td>{{ number_format($detail->gap_value, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="3" class="text-end">Totaux</th>
                                <th>{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</th>
                                <th>{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</th>
                                <th>{{ number_format($inventory->total_gap_units, 0, ',', ' ') }}</th>
                                <th>{{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($inventory->notes)
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Notes</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $inventory->notes }}</p>
                </div>
            </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
