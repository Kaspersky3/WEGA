@extends('layouts.app')

@section('title', 'Détails du Transfert ' . $transfer->reference . ' - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">{{ $transfer->reference }}</h1>
            <p class="page-subtitle">
                Transfert effectué le {{ $transfer->date_transfert?->format('d/m/Y H:i') }} par {{ $transfer->user?->name ?? 'N/A' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transfers.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-2">Lieu source</div>
                <div class="h5 mb-0">
                    <span class="badge bg-light text-dark fs-6">{{ $transfer->lieu_source }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-2">Lieu destination</div>
                <div class="h5 mb-0">
                    <span class="badge bg-primary fs-6">{{ $transfer->lieu_destination }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-2">Quantité Gros</div>
                <div class="h5 mb-0">{{ number_format($transfer->quantite_gros, 0, ',', ' ') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small mb-2">Quantité Détail</div>
                <div class="h5 mb-0">{{ number_format($transfer->quantite_detail, 0, ',', ' ') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Informations du Produit</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Code produit</th>
                        <td>{{ $transfer->product->code_produit }}</td>
                    </tr>
                    <tr>
                        <th>Libellé</th>
                        <td>{{ $transfer->product->libelle }}</td>
                    </tr>
                    <tr>
                        <th>Catégorie</th>
                        <td>{{ $transfer->product->categorie }}</td>
                    </tr>
                    <tr>
                        <th>Lieu actuel</th>
                        <td><span class="badge bg-light text-dark">{{ $transfer->product->lieu }}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Référence</th>
                        <td>{{ $transfer->reference }}</td>
                    </tr>
                    <tr>
                        <th>Date de transfert</th>
                        <td>{{ $transfer->date_transfert?->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Effectué par</th>
                        <td>{{ $transfer->user?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Date de création</th>
                        <td>{{ $transfer->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($transfer->notes)
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">Notes</h6>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $transfer->notes }}</p>
    </div>
</div>
@endif
@endsection

