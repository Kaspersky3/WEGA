@extends('layouts.app')

@section('title', 'Historique des Transferts - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Historique des Transferts</h1>
            <p class="page-subtitle">Consultez l'historique de tous vos transferts entre lieux</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau Transfert
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-box"></i> Produits
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('transfers.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Recherche</label>
                <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Référence, produit...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Lieu source</label>
                <select class="form-select" name="lieu_source">
                    <option value="">Tous</option>
                    <option value="Stock" @selected(($filters['lieu_source'] ?? '') === 'Stock')>Stock</option>
                    <option value="Boutique 1" @selected(($filters['lieu_source'] ?? '') === 'Boutique 1')>Boutique 1</option>
                    <option value="Boutique 2" @selected(($filters['lieu_source'] ?? '') === 'Boutique 2')>Boutique 2</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Lieu destination</label>
                <select class="form-select" name="lieu_destination">
                    <option value="">Tous</option>
                    <option value="Stock" @selected(($filters['lieu_destination'] ?? '') === 'Stock')>Stock</option>
                    <option value="Boutique 1" @selected(($filters['lieu_destination'] ?? '') === 'Boutique 1')>Boutique 1</option>
                    <option value="Boutique 2" @selected(($filters['lieu_destination'] ?? '') === 'Boutique 2')>Boutique 2</option>
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
            <div class="col-md-1 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
                <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Quantités</th>
                        <th>Utilisateur</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $transfer->reference }}</div>
                            </td>
                            <td>{{ $transfer->date_transfert?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $transfer->product->libelle }}</div>
                                <small class="text-muted">{{ $transfer->product->code_produit }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $transfer->lieu_source }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $transfer->lieu_destination }}</span>
                            </td>
                            <td>
                                @if($transfer->quantite_gros > 0)
                                    <div class="small"><strong>Gros:</strong> {{ number_format($transfer->quantite_gros, 0, ',', ' ') }}</div>
                                @endif
                                @if($transfer->quantite_detail > 0)
                                    <div class="small"><strong>Détail:</strong> {{ number_format($transfer->quantite_detail, 0, ',', ' ') }}</div>
                                @endif
                            </td>
                            <td>{{ $transfer->user?->name ?? 'N/A' }}</td>
                            <td class="text-end">
                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                                </div>
                                <h5 class="text-muted">Aucun transfert enregistré</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier transfert</p>
                                <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Créer un transfert
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transfers->hasPages())
    <div class="card-footer">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endsection

