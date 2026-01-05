@extends('layouts.app')

@section('title', 'Inventaire ' . $inventory->reference . ' - WEGA')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        height: 100%;
    }
    .stat-label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">{{ $inventory->reference }}</h1>
            <p class="page-subtitle">
                {{ $inventory->month_name }} · Lieu: <span class="badge bg-light text-dark">{{ $inventory->lieu ?? 'N/A' }}</span> · Réalisé le {{ $inventory->inventory_date?->format('d/m/Y H:i') }} par {{ $inventory->user?->name ?? 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'xlsx']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('inventories.details.export', ['inventory' => $inventory->id, 'format' => 'pdf']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
            <a href="{{ route('inventories.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Stock théorique</div>
            <div class="stat-value">{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Stock réel</div>
            <div class="stat-value">{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Écart total</div>
            <div class="stat-value {{ $inventory->total_gap_units >= 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($inventory->total_gap_units, 0, ',', ' ') }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Valorisation</div>
            <div class="stat-value">{{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Détails des Produits</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
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
                                <div class="small">Gros : <strong>{{ number_format($detail->stock_theorique_gros, 0, ',', ' ') }}</strong></div>
                                <div class="small">Détail : <strong>{{ number_format($detail->stock_theorique_detail, 0, ',', ' ') }}</strong></div>
                            </td>
                            <td>
                                <div class="small">Gros : <strong>{{ number_format($detail->stock_reel_gros, 0, ',', ' ') }}</strong></div>
                                <div class="small">Détail : <strong>{{ number_format($detail->stock_reel_detail, 0, ',', ' ') }}</strong></div>
                            </td>
                            <td><strong>{{ number_format($detail->stock_theorique_total, 0, ',', ' ') }}</strong></td>
                            <td><strong>{{ number_format($detail->stock_reel_total, 0, ',', ' ') }}</strong></td>
                            <td>
                                <span class="badge {{ $detail->gap_units >= 0 ? 'bg-danger' : 'bg-success' }}">
                                    {{ number_format($detail->gap_units, 0, ',', ' ') }}
                                </span>
                            </td>
                            <td><strong>{{ number_format($detail->gap_value, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3" class="text-end">Totaux</th>
                        <th><strong>{{ number_format($inventory->total_stock_theorique, 0, ',', ' ') }}</strong></th>
                        <th><strong>{{ number_format($inventory->total_stock_reel, 0, ',', ' ') }}</strong></th>
                        <th><strong>{{ number_format($inventory->total_gap_units, 0, ',', ' ') }}</strong></th>
                        <th><strong>{{ number_format($inventory->total_gap_value, 0, ',', ' ') }} FCFA</strong></th>
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
@endsection
