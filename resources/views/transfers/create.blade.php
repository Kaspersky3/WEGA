@extends('layouts.app')

@section('title', 'Nouveau Transfert - WEGA')

@push('styles')
<style>
    .suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        box-shadow: var(--shadow-lg);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        margin-top: 0.25rem;
    }
    .suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }
    .suggestion-item:hover {
        background: var(--light);
    }
    .suggestion-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Nouveau Transfert</h1>
            <p class="page-subtitle">Transférez des produits entre les lieux</p>
        </div>
        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('transfers.store') }}" method="POST" id="transferForm">
                    @csrf

                    <div class="mb-4 position-relative">
                        <label for="product_search" class="form-label fw-semibold">Rechercher un produit <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control @error('product_id') is-invalid @enderror"
                                   id="product_search"
                                   name="product_search"
                                   placeholder="Tapez le libellé ou le code produit..."
                                   autocomplete="off"
                                   required>
                        </div>
                        <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id', $product->id ?? '') }}">
                        <div id="product_suggestions" class="suggestions-dropdown" style="display: none;"></div>
                        @error('product_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-2 d-block">Commencez à taper pour voir les suggestions</small>
                    </div>

                    <div id="product_info" class="alert alert-success" style="display: {{ isset($product) ? 'block' : 'none' }};">
                        <strong>Produit sélectionné:</strong> 
                        <span id="selected_product_name">{{ $product->libelle ?? '' }}</span> 
                        (<span id="selected_product_code">{{ $product->code_produit ?? '' }}</span>)
                        @if(isset($product))
                            <br><small class="text-muted">Lieu actuel: <strong>{{ $product->lieu }}</strong></small>
                        @endif
                        <div id="stock_info" style="display: none; margin-top: 0.5rem;">
                            <hr style="margin: 0.5rem 0;">
                            <div class="fw-semibold mb-1">Stocks disponibles dans le lieu source :</div>
                            <div>Stock Gros: <strong id="available_stock_gros">0</strong></div>
                            <div>Stock Détail: <strong id="available_stock_detail">0</strong></div>
                        </div>
                    </div>

                    <div id="quantity_warning" class="alert alert-warning" style="display: none;">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Attention :</strong> 
                        <span id="warning_message"></span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="lieu_source" class="form-label fw-semibold">Lieu source <span class="text-danger">*</span></label>
                            <select class="form-select @error('lieu_source') is-invalid @enderror" id="lieu_source" name="lieu_source" required>
                                <option value="">Sélectionnez un lieu</option>
                                <option value="Stock" @selected(old('lieu_source', $product->lieu ?? '') === 'Stock')>Stock</option>
                                <option value="Boutique 1" @selected(old('lieu_source', $product->lieu ?? '') === 'Boutique 1')>Boutique 1</option>
                                <option value="Boutique 2" @selected(old('lieu_source', $product->lieu ?? '') === 'Boutique 2')>Boutique 2</option>
                            </select>
                            @error('lieu_source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lieu_destination" class="form-label fw-semibold">Lieu de destination <span class="text-danger">*</span></label>
                            <select class="form-select @error('lieu_destination') is-invalid @enderror" id="lieu_destination" name="lieu_destination" required>
                                <option value="">Sélectionnez un lieu</option>
                                <option value="Stock" @selected(old('lieu_destination') === 'Stock')>Stock</option>
                                <option value="Boutique 1" @selected(old('lieu_destination') === 'Boutique 1')>Boutique 1</option>
                                <option value="Boutique 2" @selected(old('lieu_destination') === 'Boutique 2')>Boutique 2</option>
                            </select>
                            @error('lieu_destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="quantite_gros" class="form-label fw-semibold">Quantité Gros</label>
                            <input type="number" min="0"
                                   class="form-control @error('quantite_gros') is-invalid @enderror"
                                   id="quantite_gros" name="quantite_gros" value="{{ old('quantite_gros', 0) }}">
                            <small class="text-muted" id="max_gros_hint" style="display: none;">Maximum disponible: <span id="max_gros_value">0</span></small>
                            @error('quantite_gros')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quantite_detail" class="form-label fw-semibold">Quantité Détail</label>
                            <input type="number" min="0"
                                   class="form-control @error('quantite_detail') is-invalid @enderror"
                                   id="quantite_detail" name="quantite_detail" value="{{ old('quantite_detail', 0) }}">
                            <small class="text-muted" id="max_detail_hint" style="display: none;">Maximum disponible: <span id="max_detail_value">0</span></small>
                            @error('quantite_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="date_transfert" class="form-label fw-semibold">Date de transfert <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('date_transfert') is-invalid @enderror"
                                   id="date_transfert"
                                   name="date_transfert"
                                   value="{{ old('date_transfert', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('date_transfert')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Notes (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3"
                                  placeholder="Observations, raison du transfert...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer le transfert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productSearch = document.getElementById('product_search');
    const productId = document.getElementById('product_id');
    const productSuggestions = document.getElementById('product_suggestions');
    const productInfo = document.getElementById('product_info');
    const stockInfo = document.getElementById('stock_info');
    const selectedProductName = document.getElementById('selected_product_name');
    const selectedProductCode = document.getElementById('selected_product_code');
    const lieuSource = document.getElementById('lieu_source');
    const quantiteGros = document.getElementById('quantite_gros');
    const quantiteDetail = document.getElementById('quantite_detail');
    const quantityWarning = document.getElementById('quantity_warning');
    const warningMessage = document.getElementById('warning_message');
    const availableStockGros = document.getElementById('available_stock_gros');
    const availableStockDetail = document.getElementById('available_stock_detail');
    const maxGrosHint = document.getElementById('max_gros_hint');
    const maxDetailHint = document.getElementById('max_detail_hint');
    const maxGrosValue = document.getElementById('max_gros_value');
    const maxDetailValue = document.getElementById('max_detail_value');

    let currentStockGros = 0;
    let currentStockDetail = 0;
    let currentLieu = '';

    let searchTimeout;

    productSearch.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            productSuggestions.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('api.products.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(products => {
                    if (products.length === 0) {
                        productSuggestions.innerHTML = '<div class="suggestion-item text-muted">Aucun produit trouvé</div>';
                        productSuggestions.style.display = 'block';
                        return;
                    }

                    productSuggestions.innerHTML = products.map(product => `
                        <div class="suggestion-item"
                             data-id="${product.id}"
                             data-code="${product.code_produit}"
                             data-name="${product.libelle}"
                             data-lieu="${product.lieu || 'Stock'}"
                             data-stock-gros="${product.stock_gros || 0}"
                             data-stock-detail="${product.stock_detail || 0}">
                            <strong>${product.code_produit}</strong> - ${product.libelle}
                            <br><small class="text-muted">${product.categorie} · Lieu: ${product.lieu || 'Stock'}</small>
                            <br><small class="text-muted">Stock: Gros ${product.stock_gros || 0} / Détail ${product.stock_detail || 0}</small>
                        </div>
                    `).join('');

                    productSuggestions.style.display = 'block';

                    productSuggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function() {
                            productId.value = this.dataset.id;
                            productSearch.value = this.dataset.name;
                            selectedProductName.textContent = this.dataset.name;
                            selectedProductCode.textContent = this.dataset.code;
                            lieuSource.value = this.dataset.lieu || 'Stock';
                            
                            // Stocker les valeurs de stock
                            currentStockGros = parseInt(this.dataset.stockGros || 0);
                            currentStockDetail = parseInt(this.dataset.stockDetail || 0);
                            currentLieu = this.dataset.lieu || 'Stock';
                            
                            productSuggestions.style.display = 'none';
                            productInfo.style.display = 'block';
                            
                            // Mettre à jour l'affichage des stocks
                            updateStockDisplay();
                            checkQuantities();
                        });
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                });
        }, 300);
    });

    function updateStockDisplay() {
        if (currentStockGros > 0 || currentStockDetail > 0) {
            availableStockGros.textContent = currentStockGros.toLocaleString('fr-FR');
            availableStockDetail.textContent = currentStockDetail.toLocaleString('fr-FR');
            maxGrosValue.textContent = currentStockGros.toLocaleString('fr-FR');
            maxDetailValue.textContent = currentStockDetail.toLocaleString('fr-FR');
            stockInfo.style.display = 'block';
            maxGrosHint.style.display = currentStockGros > 0 ? 'block' : 'none';
            maxDetailHint.style.display = currentStockDetail > 0 ? 'block' : 'none';
            
            // Mettre à jour le max des inputs
            quantiteGros.max = currentStockGros;
            quantiteDetail.max = currentStockDetail;
        } else {
            stockInfo.style.display = 'none';
            maxGrosHint.style.display = 'none';
            maxDetailHint.style.display = 'none';
        }
        
        // Mettre à jour le lieu dans l'info produit
        const smallElement = productInfo.querySelector('small');
        if (smallElement) {
            smallElement.innerHTML = `Lieu actuel: <strong>${currentLieu}</strong>`;
        }
    }

    function checkQuantities() {
        const qtyGros = parseInt(quantiteGros.value || 0);
        const qtyDetail = parseInt(quantiteDetail.value || 0);
        
        if (qtyGros === 0 && qtyDetail === 0) {
            quantityWarning.style.display = 'none';
            return;
        }
        
        const warnings = [];
        
        // Vérifier si la quantité à transférer est inférieure au stock disponible
        if (qtyGros > 0 && qtyGros < currentStockGros) {
            const resteGros = currentStockGros - qtyGros;
            warnings.push(`Vous transférez ${qtyGros.toLocaleString('fr-FR')} unités en gros alors qu'il y a ${currentStockGros.toLocaleString('fr-FR')} disponibles dans ${currentLieu}. Il restera ${resteGros.toLocaleString('fr-FR')} unités en gros dans le lieu source.`);
        }
        
        if (qtyDetail > 0 && qtyDetail < currentStockDetail) {
            const resteDetail = currentStockDetail - qtyDetail;
            warnings.push(`Vous transférez ${qtyDetail.toLocaleString('fr-FR')} unités en détail alors qu'il y a ${currentStockDetail.toLocaleString('fr-FR')} disponibles dans ${currentLieu}. Il restera ${resteDetail.toLocaleString('fr-FR')} unités en détail dans le lieu source.`);
        }
        
        if (warnings.length > 0) {
            warningMessage.textContent = warnings.join(' ');
            quantityWarning.style.display = 'block';
        } else {
            quantityWarning.style.display = 'none';
        }
    }

    quantiteGros.addEventListener('input', checkQuantities);
    quantiteDetail.addEventListener('input', checkQuantities);
    lieuSource.addEventListener('change', function() {
        if (productId.value && this.value) {
            checkQuantities();
        }
    });

    document.addEventListener('click', function(e) {
        if (!productSearch.contains(e.target) && !productSuggestions.contains(e.target)) {
            productSuggestions.style.display = 'none';
        }
    });

    // Initialiser si un produit est déjà sélectionné
    @if(isset($product))
        currentStockGros = {{ $product->stock_gros ?? 0 }};
        currentStockDetail = {{ $product->stock_detail ?? 0 }};
        currentLieu = '{{ $product->lieu ?? 'Stock' }}';
        updateStockDisplay();
    @endif
</script>
@endpush

