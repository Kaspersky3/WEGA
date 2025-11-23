<!DOCTYPE html>
<html>
<head>
    <title>Approvisionnement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Nouvel approvisionnement</h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('supplies.store') }}" method="POST" id="supplyForm">
                    @csrf

                    <div class="mb-3 position-relative">
                        <label for="product_search" class="form-label">Rechercher un produit <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('product_id') is-invalid @enderror" 
                               id="product_search" 
                               name="product_search" 
                               placeholder="Tapez le libellé ou le code produit..."
                               autocomplete="off"
                               required>
                        <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}">
                        <div id="product_suggestions" class="list-group mt-2 position-absolute w-100" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000; border: 1px solid #dee2e6; border-radius: 0.375rem;"></div>
                        @error('product_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Commencez à taper pour voir les suggestions</small>
                    </div>

                    <div id="product_info" class="alert alert-info" style="display: none;">
                        <strong>Produit sélectionné:</strong> <span id="selected_product_name"></span> (<span id="selected_product_code"></span>)
                    </div>

                    <div class="mb-3">
                        <label for="type_approvisionnement" class="form-label">Type d'approvisionnement <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_approvisionnement') is-invalid @enderror" 
                                id="type_approvisionnement" name="type_approvisionnement" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="Gros" {{ old('type_approvisionnement') == 'Gros' ? 'selected' : '' }}>Approvisionnement en Gros</option>
                            <option value="Détail" {{ old('type_approvisionnement') == 'Détail' ? 'selected' : '' }}>Approvisionnement en Détail</option>
                            <option value="Les deux" {{ old('type_approvisionnement') == 'Les deux' ? 'selected' : '' }}>Les deux (Gros & Détail)</option>
                        </select>
                        @error('type_approvisionnement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantite_gros" class="form-label">Quantité Gros</label>
                            <input type="number" min="0" 
                                   class="form-control @error('quantite_gros') is-invalid @enderror" 
                                   id="quantite_gros" name="quantite_gros" value="{{ old('quantite_gros', 0) }}">
                            @error('quantite_gros')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="quantite_detail" class="form-label">Quantité Détail</label>
                            <input type="number" min="0" 
                                   class="form-control @error('quantite_detail') is-invalid @enderror" 
                                   id="quantite_detail" name="quantite_detail" value="{{ old('quantite_detail', 0) }}">
                            @error('quantite_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date_approvisionnement" class="form-label">Date d'approvisionnement <span class="text-danger">*</span></label>
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

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer l'approvisionnement</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                            productSuggestions.innerHTML = '<div class="list-group-item">Aucun produit trouvé</div>';
                            productSuggestions.style.display = 'block';
                            return;
                        }

                        productSuggestions.innerHTML = products.map(product => `
                            <a href="#" class="list-group-item list-group-item-action" 
                               data-id="${product.id}" 
                               data-code="${product.code_produit}" 
                               data-name="${product.libelle}">
                                <strong>${product.code_produit}</strong> - ${product.libelle}
                                <br><small class="text-muted">${product.categorie}</small>
                            </a>
                        `).join('');

                        productSuggestions.style.display = 'block';

                        // Ajouter les événements de clic
                        productSuggestions.querySelectorAll('a').forEach(item => {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
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

        // Masquer les suggestions quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!productSearch.contains(e.target) && !productSuggestions.contains(e.target)) {
                productSuggestions.style.display = 'none';
            }
        });

        // Gérer l'affichage/masquage des champs selon le type d'approvisionnement
        typeApprovisionnement.addEventListener('change', function() {
            const type = this.value;
            
            if (type === 'Gros') {
                quantiteGros.required = true;
                quantiteDetail.required = false;
                quantiteDetail.parentElement.style.display = 'block';
                quantiteDetail.value = 0;
            } else if (type === 'Détail') {
                quantiteGros.required = false;
                quantiteDetail.required = true;
                quantiteGros.parentElement.style.display = 'block';
                quantiteGros.value = 0;
            } else if (type === 'Les deux') {
                quantiteGros.required = true;
                quantiteDetail.required = true;
                quantiteGros.parentElement.style.display = 'block';
                quantiteDetail.parentElement.style.display = 'block';
            } else {
                quantiteGros.required = false;
                quantiteDetail.required = false;
            }
        });

        // Déclencher l'événement au chargement si une valeur existe
        if (typeApprovisionnement.value) {
            typeApprovisionnement.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>

