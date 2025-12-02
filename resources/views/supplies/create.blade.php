@extends('layouts.app')

@section('title', 'Nouvel Approvisionnement - WEGA')

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
            <h1 class="page-title">Nouvel Approvisionnement</h1>
            <p class="page-subtitle">Enregistrez un nouvel approvisionnement de stock</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('supplies.store') }}" method="POST" id="supplyForm">
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
                        <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}">
                        <div id="product_suggestions" class="suggestions-dropdown" style="display: none;"></div>
                        @error('product_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted mt-2 d-block">Commencez à taper pour voir les suggestions</small>
                    </div>

                    <div id="product_info" class="alert alert-success" style="display: none;">
                        <strong>Produit sélectionné:</strong> <span id="selected_product_name"></span> (<span id="selected_product_code"></span>)
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="type_approvisionnement" class="form-label fw-semibold">Type d'approvisionnement <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_approvisionnement') is-invalid @enderror" id="type_approvisionnement" name="type_approvisionnement" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Gros" {{ old('type_approvisionnement') == 'Gros' ? 'selected' : '' }}>Approvisionnement en Gros</option>
                                <option value="Détail" {{ old('type_approvisionnement') == 'Détail' ? 'selected' : '' }}>Approvisionnement en Détail</option>
                                <option value="Les deux" {{ old('type_approvisionnement') == 'Les deux' ? 'selected' : '' }}>Les deux (Gros & Détail)</option>
                            </select>
                            @error('type_approvisionnement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_approvisionnement" class="form-label fw-semibold">Date d'approvisionnement <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('date_approvisionnement') is-invalid @enderror"
                                   id="date_approvisionnement"
                                   name="date_approvisionnement"
                                   value="{{ old('date_approvisionnement', date('Y-m-d')) }}"
                                   required>
                            @error('date_approvisionnement')
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
                            @error('quantite_gros')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="quantite_detail" class="form-label fw-semibold">Quantité Détail</label>
                            <input type="number" min="0"
                                   class="form-control @error('quantite_detail') is-invalid @enderror"
                                   id="quantite_detail" name="quantite_detail" value="{{ old('quantite_detail', 0) }}">
                            @error('quantite_detail')
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
                                  placeholder="Observations, fournisseur, etc...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer l'approvisionnement
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
    const selectedProductName = document.getElementById('selected_product_name');
    const selectedProductCode = document.getElementById('selected_product_code');
    const typeApprovisionnement = document.getElementById('type_approvisionnement');
    const quantiteGros = document.getElementById('quantite_gros');
    const quantiteDetail = document.getElementById('quantite_detail');

    let searchTimeout;

    productSearch.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            productSuggestions.style.display = 'none';
            productInfo.style.display = 'none';
            productId.value = '';
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
                             data-name="${product.libelle}">
                            <strong>${product.code_produit}</strong> - ${product.libelle}
                            <br><small class="text-muted">${product.categorie}</small>
                        </div>
                    `).join('');

                    productSuggestions.style.display = 'block';

                    productSuggestions.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function() {
                            productId.value = this.dataset.id;
                            productSearch.value = this.dataset.name;
                            selectedProductName.textContent = this.dataset.name;
                            selectedProductCode.textContent = this.dataset.code;
                            productSuggestions.style.display = 'none';
                            productInfo.style.display = 'block';
                        });
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!productSearch.contains(e.target) && !productSuggestions.contains(e.target)) {
            productSuggestions.style.display = 'none';
        }
    });

    typeApprovisionnement.addEventListener('change', function() {
        const type = this.value;

        if (type === 'Gros') {
            quantiteGros.required = true;
            quantiteDetail.required = false;
            quantiteDetail.value = 0;
        } else if (type === 'Détail') {
            quantiteGros.required = false;
            quantiteDetail.required = true;
            quantiteGros.value = 0;
        } else if (type === 'Les deux') {
            quantiteGros.required = true;
            quantiteDetail.required = true;
        } else {
            quantiteGros.required = false;
            quantiteDetail.required = false;
        }
    });

    if (typeApprovisionnement.value) {
        typeApprovisionnement.dispatchEvent(new Event('change'));
    }
</script>
@endpush
