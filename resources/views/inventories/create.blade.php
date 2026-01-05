@extends('layouts.app')

@section('title', 'Créer un Inventaire - WEGA')

@push('styles')
<style>
    .column-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .summary-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        height: 100%;
    }
    .summary-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }
    .sticky-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .gap-positive {
        color: var(--danger);
    }
    .gap-negative {
        color: var(--success);
    }
    .inventory-table-container {
        max-height: 70vh;
        overflow-y: auto;
    }
    .import-card {
        border: 2px solid var(--primary);
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Créer un Inventaire</h1>
            <p class="page-subtitle">Validez le stock réel de vos produits</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Historique
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-box"></i> Produits
            </a>
        </div>
    </div>
</div>

<div class="card import-card mb-4">
    <div class="card-header" style="background: var(--primary); color: white;">
        <h5 class="mb-0">
            <i class="bi bi-upload"></i> Importer un inventaire depuis un fichier CSV/Excel
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('inventories.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Lieu <span class="text-danger">*</span></label>
                    <select class="form-select @error('lieu') is-invalid @enderror" name="lieu" required>
                        <option value="">Sélectionnez un lieu</option>
                        <option value="Stock" {{ old('lieu') == 'Stock' ? 'selected' : '' }}>Stock</option>
                        <option value="Boutique 1" {{ old('lieu') == 'Boutique 1' ? 'selected' : '' }}>Boutique 1</option>
                        <option value="Boutique 2" {{ old('lieu') == 'Boutique 2' ? 'selected' : '' }}>Boutique 2</option>
                    </select>
                    @error('lieu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fichier CSV/Excel</label>
                    <input type="file" name="csv_file" class="form-control" accept=".csv,.txt,.xlsx" required>
                    <small class="form-text text-muted mt-1 d-block">
                        Format attendu : Code produit, Stock réel gros, Stock réel détail<br>
                        Ou format d'export complet avec en-têtes
                    </small>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Date d'inventaire</label>
                    <input type="date" name="inventory_date" class="form-control" value="{{ old('inventory_date', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="1" class="form-control" placeholder="Observations...">{{ old('notes') }}</textarea>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload"></i> Importer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('inventories.create') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold">Lieu <span class="text-danger">*</span></label>
                <select class="form-select @error('lieu') is-invalid @enderror" name="lieu" required>
                    <option value="">Sélectionnez un lieu</option>
                    <option value="Stock" @selected(($filters['lieu'] ?? null) === 'Stock')>Stock</option>
                    <option value="Boutique 1" @selected(($filters['lieu'] ?? null) === 'Boutique 1')>Boutique 1</option>
                    <option value="Boutique 2" @selected(($filters['lieu'] ?? null) === 'Boutique 2')>Boutique 2</option>
                </select>
                @error('lieu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Catégorie</label>
                <select class="form-select" name="categorie">
                    <option value="">Toutes les catégories</option>
                    @foreach($products->pluck('product.categorie')->unique()->filter()->sort() as $categorie)
                        <option value="{{ $categorie }}" @selected(($filters['categorie'] ?? null) === $categorie)>{{ $categorie }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Nom / Référence</label>
                <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Rechercher...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Code produit</label>
                <input type="text" class="form-control" name="reference" value="{{ $filters['reference'] ?? '' }}" placeholder="P001...">
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel"></i> Filtrer
                </button>
                <a href="{{ route('inventories.create') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

@if(isset($requireLieu) && $requireLieu)
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle"></i> <strong>Important :</strong> Veuillez sélectionner un lieu pour commencer l'inventaire. Un inventaire ne peut concerner qu'un seul lieu à la fois.
</div>
@endif

<form action="{{ route('inventories.store') }}" method="POST" id="inventoryForm">
    @csrf
    <input type="hidden" name="filters[categorie]" value="{{ $filters['categorie'] ?? '' }}">
    <input type="hidden" name="filters[search]" value="{{ $filters['search'] ?? '' }}">
    <input type="hidden" name="filters[reference]" value="{{ $filters['reference'] ?? '' }}">

    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <label class="form-label fw-semibold">Lieu <span class="text-danger">*</span></label>
            <select class="form-select @error('lieu') is-invalid @enderror" name="lieu" required>
                <option value="">Sélectionnez un lieu</option>
                <option value="Stock" @selected(old('lieu', $filters['lieu'] ?? '') === 'Stock')>Stock</option>
                <option value="Boutique 1" @selected(old('lieu', $filters['lieu'] ?? '') === 'Boutique 1')>Boutique 1</option>
                <option value="Boutique 2" @selected(old('lieu', $filters['lieu'] ?? '') === 'Boutique 2')>Boutique 2</option>
            </select>
            @error('lieu')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold">Date d'inventaire</label>
            <input type="date" name="inventory_date" class="form-control" value="{{ old('inventory_date', $inventoryDate) }}" required>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-semibold">Notes</label>
            <textarea name="notes" rows="1" class="form-control" placeholder="Observations, incidents...">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="row g-3 mb-4" id="inventorySummary">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="column-title">Stock théorique (unités détail)</div>
                <h4 class="summary-value mb-0" id="summaryTheoretical">{{ number_format($theoreticalTotal, 0, ',', ' ') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="column-title">Stock réel total</div>
                <h4 class="summary-value mb-0" id="summaryReal">0</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="column-title">Écart (unités)</div>
                <h4 class="summary-value mb-0" id="summaryGap">0</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="column-title">Valorisation de l'écart (FCFA)</div>
                <h4 class="summary-value mb-0" id="summaryValue">0</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="inventory-table-container">
                <table class="table mb-0" id="inventoryTable">
                    <thead class="sticky-header">
                        <tr>
                            <th style="width: 20%;">Produit</th>
                            <th style="width: 20%;">Stock théorique</th>
                            <th style="width: 30%;">Stock réel</th>
                            <th style="width: 10%;">Conversion</th>
                            <th style="width: 10%;">Total réel</th>
                            <th style="width: 10%;">Écart & valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $row)
                            @php($product = $row['product'])
                            <tr data-row
                                data-theoretical-total="{{ $row['stock_theorique_total'] }}"
                                data-conversion="{{ $row['conversion_rate'] }}"
                                data-unit-price="{{ $row['unit_purchase_price'] }}">
                                <td>
                                    <div class="fw-semibold">{{ $product->libelle }}</div>
                                    <small class="text-muted d-block">{{ $product->code_produit }} · {{ $product->categorie }}</small>
                                    <span class="badge bg-light text-dark mt-1">{{ $product->type_produit ?? 'N/A' }}</span>
                                </td>
                                <td class="bg-light">
                                    <div class="column-title">Stock théorique</div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Gros</span>
                                        <strong>{{ number_format($row['stock_theorique_gros'], 0, ',', ' ') }} {{ $row['bulk_unit_label'] }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Détail</span>
                                        <strong>{{ number_format($row['stock_theorique_detail'], 0, ',', ' ') }} {{ $row['detail_unit_label'] }}</strong>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Total (détail)</span>
                                        <strong class="text-primary">{{ number_format($row['stock_theorique_total'], 0, ',', ' ') }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="column-title">Saisie du stock réel</div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Stock réel gros</label>
                                            <input type="number"
                                                   min="0"
                                                   class="form-control form-control-sm js-real-gros"
                                                   name="products[{{ $index }}][stock_reel_gros]"
                                                   value="{{ old("products.$index.stock_reel_gros", 0) }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Stock réel détail</label>
                                            <input type="number"
                                                   min="0"
                                                   class="form-control form-control-sm js-real-detail"
                                                   name="products[{{ $index }}][stock_reel_detail]"
                                                   value="{{ old("products.$index.stock_reel_detail", 0) }}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id }}">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">
                                        1 {{ $row['bulk_unit_label'] }} = {{ $row['conversion_rate'] }} {{ $row['detail_unit_label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="column-title">Quantité totale</div>
                                    <strong class="js-total-real">0</strong>
                                </td>
                                <td>
                                    <div class="column-title">Écart</div>
                                    <div class="js-gap fw-semibold">0</div>
                                    <small class="text-muted js-gap-value">0 FCFA</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                                    </div>
                                    <h5 class="text-muted">Aucun produit trouvé</h5>
                                    <p class="text-muted mb-4">Aucun produit ne correspond aux filtres sélectionnés.</p>
                                    <a href="{{ route('inventories.create') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-clockwise"></i> Réinitialiser les filtres
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-x-circle"></i> Annuler
        </a>
        <button type="submit" class="btn btn-primary btn-lg" @disabled($products->isEmpty())>
            <i class="bi bi-check-circle"></i> Valider l'inventaire
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('#inventoryTable [data-row]');

        const summary = {
            theoretical: document.getElementById('summaryTheoretical'),
            real: document.getElementById('summaryReal'),
            gap: document.getElementById('summaryGap'),
            value: document.getElementById('summaryValue'),
        };

        function formatNumber(value) {
            return Number(value).toLocaleString('fr-FR');
        }

        function refreshSummary() {
            let theoretical = 0;
            let real = 0;
            let gap = 0;
            let value = 0;

            rows.forEach(row => {
                theoretical += Number(row.dataset.theoreticalTotal ?? 0);
                real += Number(row.dataset.realTotal ?? 0);
                gap += Number(row.dataset.gapUnits ?? 0);
                value += Number(row.dataset.gapValue ?? 0);
            });

            summary.theoretical.textContent = formatNumber(theoretical);
            summary.real.textContent = formatNumber(real);
            summary.gap.textContent = formatNumber(gap);
            summary.value.textContent = formatNumber(value.toFixed(0));
        }

        rows.forEach(row => {
            const conversion = Number(row.dataset.conversion || 1);
            const unitPrice = Number(row.dataset.unitPrice || 0);
            const theoreticalTotal = Number(row.dataset.theoreticalTotal || 0);
            const inputGros = row.querySelector('.js-real-gros');
            const inputDetail = row.querySelector('.js-real-detail');
            const totalReal = row.querySelector('.js-total-real');
            const gapEl = row.querySelector('.js-gap');
            const gapValueEl = row.querySelector('.js-gap-value');

            const recalc = () => {
                const gros = Number(inputGros?.value || 0);
                const detail = Number(inputDetail?.value || 0);
                const realTotal = (gros * conversion) + detail;
                const gap = theoreticalTotal - realTotal;
                const gapValue = gap * unitPrice;

                totalReal.textContent = formatNumber(realTotal);
                gapEl.textContent = formatNumber(gap);
                gapEl.classList.remove('gap-positive', 'gap-negative');
                gapEl.classList.add(gap > 0 ? 'gap-positive' : gap < 0 ? 'gap-negative' : '');
                gapValueEl.textContent = `${formatNumber(gapValue.toFixed(0))} FCFA`;

                row.dataset.realTotal = realTotal;
                row.dataset.gapUnits = gap;
                row.dataset.gapValue = gapValue;

                refreshSummary();
            };

            inputGros?.addEventListener('input', recalc);
            inputDetail?.addEventListener('input', recalc);
            recalc();
        });
    });
</script>
@endpush
