@extends('layouts.app')

@section('title', $product->libelle . ' - WEGA')

@push('styles')
<style>
    .info-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--border);
        height: 100%;
    }
    .info-card-header {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    .stat-badge {
        font-size: 1.5rem;
        font-weight: 700;
        padding: 0.5rem 0;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">{{ $product->libelle }}</h1>
            <p class="page-subtitle">Code: {{ $product->code_produit }} · {{ $product->categorie }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('supplies.create') }}" class="btn btn-success">
                <i class="bi bi-cart-plus"></i> Approvisionnement
            </a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
            @auth
                @if(auth()->user()->isAdmin())
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action supprimera également tous les approvisionnements et ventes associés. Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </form>
                @endif
            @endauth
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="info-card">
            <div class="info-card-header">Informations Générales</div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width: 40%;">Code Produit</td>
                    <td><strong>{{ $product->code_produit }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Libellé</td>
                    <td>{{ $product->libelle }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Catégorie</td>
                    <td>{{ $product->categorie }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Type</td>
                    <td>
                        @if($product->type_produit)
                            <span class="badge bg-info">{{ $product->type_produit }}</span>
                        @else
                            <span class="badge bg-secondary">Non défini</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Lieu</td>
                    <td><span class="badge bg-light text-dark">{{ $product->lieu }}</span></td>
                </tr>
                <tr>
                    <td class="text-muted">Date d'enregistrement</td>
                    <td>{{ $product->created_at->format('d/m/Y à H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-card">
            <div class="info-card-header">Stocks Actuels</div>
            <table class="table table-borderless mb-0">
                @php
                    $hasStockGros = ($product->stock_gros ?? 0) > 0 || ($product->type_produit == 'Gros' || $product->type_produit == 'Les deux');
                    $hasStockDetail = ($product->stock_detail ?? 0) > 0 || ($product->type_produit == 'Détail' || $product->type_produit == 'Les deux');
                    $hasBothStocks = $hasStockGros && $hasStockDetail;
                @endphp
                @if($hasStockGros)
                <tr>
                    <td class="text-muted" style="width: 50%;">Stock Gros</td>
                    <td><span class="badge bg-primary stat-badge">{{ number_format($product->stock_gros ?? 0, 0, ',', ' ') }}</span></td>
                </tr>
                @endif
                @if($hasStockDetail)
                <tr>
                    <td class="text-muted">Stock Détail</td>
                    <td><span class="badge bg-success stat-badge">{{ number_format($product->stock_detail ?? 0, 0, ',', ' ') }}</span></td>
                </tr>
                @endif
                @if($hasBothStocks)
                <tr>
                    <td class="text-muted"><strong>Stock Total</strong></td>
                    <td><strong class="stat-badge">{{ number_format(($product->stock_gros ?? 0) + ($product->stock_detail ?? 0), 0, ',', ' ') }}</strong></td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
    <div class="col-md-6">
        <div class="info-card">
            <div class="info-card-header">Prix Gros (FCFA)</div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width: 50%;">Prix d'achat</td>
                    <td><strong>{{ number_format($product->prix_achat_gros ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Prix de vente</td>
                    <td><strong>{{ number_format($product->prix_vente_gros ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
    <div class="col-md-6">
        <div class="info-card">
            <div class="info-card-header">Prix Détail (FCFA)</div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width: 50%;">Prix d'achat</td>
                    <td><strong>{{ number_format($product->prix_achat_detail ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Prix de vente</td>
                    <td><strong>{{ number_format($product->prix_vente_detail ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                </tr>
            </table>
        </div>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistiques de Ventes</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Quantités vendues</h6>
                        <table class="table table-borderless mb-0">
                            @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
                            <tr>
                                <td class="text-muted" style="width: 60%;">Quantité vendue Gros</td>
                                <td><strong>{{ number_format($product->quantite_vendue_gros ?? 0, 0, ',', ' ') }}</strong></td>
                            </tr>
                            @endif
                            @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
                            <tr>
                                <td class="text-muted">Quantité vendue Détail</td>
                                <td><strong>{{ number_format($product->quantite_vendue_detail ?? 0, 0, ',', ' ') }}</strong></td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td class="text-muted"><strong>Quantité totale vendue</strong></td>
                                <td><strong>{{ number_format(($product->quantite_vendue_gros ?? 0) + ($product->quantite_vendue_detail ?? 0), 0, ',', ' ') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Montants</h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 60%;">Montant total des ventes</td>
                                <td><strong class="text-success">{{ number_format($product->montant_total_ventes ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Montant total des achats</td>
                                <td><strong class="text-danger">{{ number_format($product->montant_total_achats ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted"><strong>Marge bénéficiaire</strong></td>
                                <td>
                                    <strong class="{{ $margeBeneficiaire >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($margeBeneficiaire, 0, ',', ' ') }} FCFA
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($dernierApprovisionnement)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dernier Approvisionnement</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 20%;">Date</td>
                        <td>{{ \Carbon\Carbon::parse($dernierApprovisionnement->date_approvisionnement)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Type</td>
                        <td><span class="badge bg-primary">{{ $dernierApprovisionnement->type_approvisionnement }}</span></td>
                    </tr>
                    @if($dernierApprovisionnement->type_approvisionnement == 'Gros' || $dernierApprovisionnement->type_approvisionnement == 'Les deux')
                    <tr>
                        <td class="text-muted">Quantité Gros</td>
                        <td>{{ number_format($dernierApprovisionnement->quantite_gros, 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                    @if($dernierApprovisionnement->type_approvisionnement == 'Détail' || $dernierApprovisionnement->type_approvisionnement == 'Les deux')
                    <tr>
                        <td class="text-muted">Quantité Détail</td>
                        <td>{{ number_format($dernierApprovisionnement->quantite_detail, 0, ',', ' ') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@if($product->supplies->count() > 0)
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Historique des Approvisionnements</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantité Gros</th>
                                <th>Quantité Détail</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->supplies->sortByDesc('date_approvisionnement') as $supply)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($supply->date_approvisionnement)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-primary">{{ $supply->type_approvisionnement }}</span></td>
                                    <td>{{ number_format($supply->quantite_gros ?? 0, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($supply->quantite_detail ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-muted">{{ $supply->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
