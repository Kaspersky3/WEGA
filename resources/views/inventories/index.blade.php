@extends('layouts.app')

@section('title', 'Historique des Inventaires - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Historique des Inventaires</h1>
            <p class="page-subtitle">Consultez l'historique de tous vos inventaires</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouvel Inventaire
            </a>
            <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-right"></i> Transferts
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-box"></i> Produits
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('inventories.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Recherche</label>
                <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Référence, libellé...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Lieu</label>
                <select class="form-select" name="lieu">
                    <option value="">Tous les lieux</option>
                    <option value="Stock" @selected(($filters['lieu'] ?? '') === 'Stock')>Stock</option>
                    <option value="Boutique 1" @selected(($filters['lieu'] ?? '') === 'Boutique 1')>Boutique 1</option>
                    <option value="Boutique 2" @selected(($filters['lieu'] ?? '') === 'Boutique 2')>Boutique 2</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Statut</label>
                <select class="form-select" name="status">
                    <option value="">Tous</option>
                    <option value="validated" @selected(($filters['status'] ?? '') === 'validated')>Validé</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Brouillon</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Du</label>
                <input type="date" class="form-control" name="from" value="{{ $filters['from'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Au</label>
                <input type="date" class="form-control" name="to" value="{{ $filters['to'] ?? '' }}">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="{{ route('inventories.history.export', array_merge($filters, ['format' => 'xlsx'])) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-file-earmark-excel"></i> Exporter Excel
    </a>
    <a href="{{ route('inventories.history.export', array_merge($filters, ['format' => 'pdf'])) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Exporter PDF
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    @php
                        $currentSort = $filters['sort'] ?? 'inventory_date';
                        $currentDirection = $filters['direction'] ?? 'desc';
                        $sortLink = function (string $column, string $label) use ($filters, $currentSort, $currentDirection) {
                            $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
                            $params = array_merge($filters, ['sort' => $column, 'direction' => $direction]);
                            $icon = $currentSort === $column ? ($currentDirection === 'asc' ? '↑' : '↓') : '';
                            $url = route('inventories.index', $params);
                            return "<a href=\"{$url}\" class=\"text-decoration-none text-dark\">{$label} {$icon}</a>";
                        };
                    @endphp
                    <tr>
                        <th>{!! $sortLink('reference', 'Référence') !!}</th>
                        <th>{!! $sortLink('inventory_date', 'Date') !!}</th>
                        <th>Lieu</th>
                        <th>Utilisateur</th>
                        <th>Stock théorique</th>
                        <th>Stock réel</th>
                        <th>{!! $sortLink('total_gap_units', 'Écart (unités)') !!}</th>
                        <th>{!! $sortLink('total_gap_value', 'Valorisation') !!}</th>
                        <th class="text-end">Actions</th>
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
                            <td>
                                <span class="badge bg-light text-dark">{{ $inventory->lieu ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $inventory->user?->name ?? 'N/A' }}</td>
                            <td>{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</td>
                            <td>{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</td>
                            <td>
                                <span class="badge {{ $inventory->total_gap_units >= 0 ? 'bg-danger' : 'bg-success' }}">
                                    {{ number_format($inventory->total_gap_units, 0, ',', ' ') }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA</strong>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('inventories.show', $inventory) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Consulter
                                    </a>
                                    <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'xlsx']) }}" class="btn btn-sm btn-outline-secondary" title="Exporter Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                    <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-secondary" title="Exporter PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                                </div>
                                <h5 class="text-muted">Aucun inventaire enregistré</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier inventaire</p>
                                <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Créer un inventaire
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($inventories->hasPages())
    <div class="card-footer">
        {{ $inventories->links() }}
    </div>
    @endif
</div>
@endsection
