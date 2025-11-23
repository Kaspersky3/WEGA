<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire - Comptage physique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f9fc; }
        .column-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; }
        .summary-card { border-radius: 1rem; }
        .table thead th { vertical-align: middle; }
        .table tbody td { vertical-align: middle; }
        .sticky-header { position: sticky; top: 0; background: #fff; z-index: 10; }
        .gap-positive { color: #dc3545; }
        .gap-negative { color: #198754; }
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <p class="text-muted mb-1">Inventaire physique</p>
                <h1 class="h3 mb-0">Valider le stock réel des produits</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">Historique</a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Produits</a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('inventories.create') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Catégorie</label>
                        <select class="form-select" name="categorie">
                            <option value="">Toutes les catégories</option>
                            @foreach($products->pluck('product.categorie')->unique()->filter()->sort() as $categorie)
                                <option value="{{ $categorie }}" @selected(($filters['categorie'] ?? null) === $categorie)>{{ $categorie }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nom / Référence</label>
                        <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Rechercher...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Code produit</label>
                        <input type="text" class="form-control" name="reference" value="{{ $filters['reference'] ?? '' }}" placeholder="P001...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-50">Filtrer</button>
                        <a href="{{ route('inventories.create') }}" class="btn btn-light w-50">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>

        <form action="{{ route('inventories.store') }}" method="POST" id="inventoryForm">
            @csrf
            <input type="hidden" name="filters[categorie]" value="{{ $filters['categorie'] ?? '' }}">
            <input type="hidden" name="filters[search]" value="{{ $filters['search'] ?? '' }}">
            <input type="hidden" name="filters[reference]" value="{{ $filters['reference'] ?? '' }}">

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Date d'inventaire</label>
                    <input type="date" name="inventory_date" class="form-control" value="{{ old('inventory_date', $inventoryDate) }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="1" class="form-control" placeholder="Observations, incidents...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="row g-3 mb-4" id="inventorySummary">
                <div class="col-md-3">
                    <div class="card summary-card shadow-sm">
                        <div class="card-body">
                            <p class="column-title mb-1">Stock théorique (unités détail)</p>
                            <h4 class="mb-0" id="summaryTheoretical">{{ number_format($theoreticalTotal, 0, ',', ' ') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card shadow-sm">
                        <div class="card-body">
                            <p class="column-title mb-1">Stock réel total</p>
                            <h4 class="mb-0" id="summaryReal">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card shadow-sm">
                        <div class="card-body">
                            <p class="column-title mb-1">Écart (unités)</p>
                            <h4 class="mb-0" id="summaryGap">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card shadow-sm">
                        <div class="card-body">
                            <p class="column-title mb-1">Valorisation de l'écart (FCFA)</p>
                            <h4 class="mb-0" id="summaryValue">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 70vh;">
                        <table class="table table-hover align-middle mb-0" id="inventoryTable">
                            <thead class="sticky-header">
                                <tr class="text-center">
                                    <th style="width: 20%;">Produit</th>
                                    <th style="width: 20%;">Colonne A - Stock théorique</th>
                                    <th style="width: 30%;">Colonne B - Stock réel</th>
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
                                            <span class="badge bg-light text-dark mt-2">{{ $product->type_produit ?? 'N/A' }}</span>
                                        </td>
                                        <td class="bg-light">
                                            <div class="column-title mb-1">Colonne A</div>
                                            <div class="d-flex justify-content-between">
                                                <span>Gros</span>
                                                <strong>{{ number_format($row['stock_theorique_gros'], 0, ',', ' ') }} {{ $row['bulk_unit_label'] }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Détail</span>
                                                <strong>{{ number_format($row['stock_theorique_detail'], 0, ',', ' ') }} {{ $row['detail_unit_label'] }}</strong>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between">
                                                <span>Total (détail)</span>
                                                <strong class="text-primary">{{ number_format($row['stock_theorique_total'], 0, ',', ' ') }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="column-title mb-2">Colonne B</div>
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
                                            <div class="column-title mb-1">Quantité totale</div>
                                            <strong class="js-total-real">0</strong>
                                        </td>
                                        <td>
                                            <div class="column-title mb-1">Écart</div>
                                            <div class="js-gap fw-semibold">0</div>
                                            <small class="text-muted js-gap-value">0 FCFA</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <p class="mb-2">Aucun produit ne correspond aux filtres sélectionnés.</p>
                                            <a href="{{ route('inventories.create') }}" class="btn btn-outline-primary btn-sm">Réinitialiser les filtres</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
                <button type="submit" class="btn btn-primary btn-lg" @disabled($products->isEmpty())>
                    Valider l’inventaire
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                    gapEl.classList.toggle('gap-positive', gap > 0);
                    gapEl.classList.toggle('gap-negative', gap < 0);
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
</body>
</html>