<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des inventaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <p class="text-muted mb-1">Pilotage des stocks</p>
                <h1 class="h3 mb-0">Historique des inventaires</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('inventories.create') }}" class="btn btn-primary">Valider un inventaire</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Produits</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('inventories.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Référence, libellé...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="status">
                            <option value="">Tous</option>
                            <option value="validated" @selected(($filters['status'] ?? '') === 'validated')>Validé</option>
                            <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Brouillon</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Du</label>
                        <input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Au</label>
                        <input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-50">Filtrer</button>
                        <a href="{{ route('inventories.index') }}" class="btn btn-light w-50">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="{{ route('inventories.history.export', array_merge($filters, ['format' => 'csv'])) }}" class="btn btn-outline-secondary btn-sm">Exporter CSV</a>
            <a href="{{ route('inventories.history.export', array_merge($filters, ['format' => 'xlsx'])) }}" class="btn btn-outline-secondary btn-sm">Exporter Excel</a>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        @php
                            $currentSort = $filters['sort'] ?? 'inventory_date';
                            $currentDirection = $filters['direction'] ?? 'desc';
                            $sortLink = function (string $column, string $label) use ($filters, $currentSort, $currentDirection) {
                                $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                                $params = array_merge($filters, ['sort' => $column, 'direction' => $direction]);
                                $icon = $currentSort === $column ? ($currentDirection === 'asc' ? '↑' : '↓') : '';
                                $url = route('inventories.index', $params);
                                return "<a href=\"{$url}\" class=\"text-decoration-none\">{$label} {$icon}</a>";
                            };
                        @endphp
                        <tr>
                            <th>{!! $sortLink('reference', 'Référence') !!}</th>
                            <th>{!! $sortLink('inventory_date', 'Date') !!}</th>
                            <th>Utilisateur</th>
                            <th>Stock théorique</th>
                            <th>Stock réel</th>
                            <th>{!! $sortLink('total_gap_units', 'Écart (unités)') !!}</th>
                            <th>{!! $sortLink('total_gap_value', 'Valorisation') !!}</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $inventory->reference }}</div>
                                    <small class="text-muted">{{ $inventory->month_name }}</small>
                                </td>
                                <td>{{ $inventory->inventory_date?->format('d/m/Y H:i') }}</td>
                                <td>{{ $inventory->user?->name ?? 'N/A' }}</td>
                                <td>{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</td>
                                <td>{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</td>
                                <td class="{{ $inventory->total_gap_units >= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($inventory->total_gap_units, 0, ',', ' ') }}
                                </td>
                                <td>
                                    {{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="d-flex gap-2">
                                    <a href="{{ route('inventories.show', $inventory) }}" class="btn btn-sm btn-outline-primary">Consulter</a>
                                    <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">CSV</a>
                                    <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'xlsx']) }}" class="btn btn-sm btn-outline-secondary">Excel</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <p class="mb-2">Aucun inventaire encore enregistré.</p>
                                    <a href="{{ route('inventories.create') }}" class="btn btn-primary btn-sm">Créer mon premier inventaire</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
