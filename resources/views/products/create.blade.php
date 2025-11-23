<!DOCTYPE html>
<html>
<head>
    <title>Créer un produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h1 class="mb-4">Créer un nouveau produit</h1>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.store') }}" method="POST" id="productForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select @error('categorie') is-invalid @enderror" 
                                    id="categorie" name="categorie" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="Vin & Boissons" {{ old('categorie') == 'Vin & Boissons' ? 'selected' : '' }}>Vin & Boissons</option>
                                <option value="Produits Alimentaires" {{ old('categorie') == 'Produits Alimentaires' ? 'selected' : '' }}>Produits Alimentaires</option>
                                <option value="Produits de vitrine" {{ old('categorie') == 'Produits de vitrine' ? 'selected' : '' }}>Produits de vitrine</option>
                                <option value="Fournitures scolaires" {{ old('categorie') == 'Fournitures scolaires' ? 'selected' : '' }}>Fournitures scolaires</option>
                                <option value="Savons & Autres" {{ old('categorie') == 'Savons & Autres' ? 'selected' : '' }}>Savons & Autres</option>
                                <option value="Biscuits & BonBon" {{ old('categorie') == 'Biscuits & BonBon' ? 'selected' : '' }}>Biscuits & BonBon</option>
                            </select>
                            @error('categorie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                                   id="libelle" name="libelle" value="{{ old('libelle') }}" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type_produit" class="form-label">Type de produit <span class="text-danger">*</span></label>
                            <select class="form-select @error('type_produit') is-invalid @enderror" 
                                    id="type_produit" name="type_produit" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Gros" {{ old('type_produit') == 'Gros' ? 'selected' : '' }}>En Gros</option>
                                <option value="Détail" {{ old('type_produit') == 'Détail' ? 'selected' : '' }}>En Détail</option>
                                <option value="Les deux" {{ old('type_produit') == 'Les deux' ? 'selected' : '' }}>Les deux (Gros & Détail)</option>
                            </select>
                            @error('type_produit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <select class="form-select @error('lieu') is-invalid @enderror" 
                                    id="lieu" name="lieu" required>
                                <option value="">Sélectionnez un lieu</option>
                                <option value="Stock" {{ old('lieu') == 'Stock' ? 'selected' : '' }}>Stock</option>
                                <option value="Boutique" {{ old('lieu') == 'Boutique' ? 'selected' : '' }}>Boutique</option>
                            </select>
                            @error('lieu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3" id="bulk_unit_group">
                            <label for="bulk_unit_label" class="form-label">Unité gros</label>
                            <input type="text" class="form-control @error('bulk_unit_label') is-invalid @enderror"
                                   id="bulk_unit_label" name="bulk_unit_label" value="{{ old('bulk_unit_label', 'Carton') }}">
                            @error('bulk_unit_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="detail_unit_label" class="form-label">Unité détail <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('detail_unit_label') is-invalid @enderror"
                                   id="detail_unit_label" name="detail_unit_label" value="{{ old('detail_unit_label', 'Pièce') }}" required>
                            @error('detail_unit_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3" id="conversion_group">
                            <label for="units_per_bulk" class="form-label">Conversion (1 gros = ? détail)</label>
                            <input type="number" min="1" class="form-control @error('units_per_bulk') is-invalid @enderror"
                                   id="units_per_bulk" name="units_per_bulk" value="{{ old('units_per_bulk', 1) }}">
                            @error('units_per_bulk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Section Prix et Stock Gros -->
                    <div id="section_gros" class="border p-3 mb-3 rounded" style="display: none;">
                        <h5 class="mb-3">Informations Gros</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prix_achat_gros" class="form-label">Prix d'achat Gros (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('prix_achat_gros') is-invalid @enderror" 
                                       id="prix_achat_gros" name="prix_achat_gros" value="{{ old('prix_achat_gros') }}">
                                @error('prix_achat_gros')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="prix_vente_gros" class="form-label">Prix de vente Gros (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('prix_vente_gros') is-invalid @enderror" 
                                       id="prix_vente_gros" name="prix_vente_gros" value="{{ old('prix_vente_gros') }}">
                                @error('prix_vente_gros')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock_gros" class="form-label">Stock Gros <span class="text-danger">*</span></label>
                                <input type="number" min="0" 
                                       class="form-control @error('stock_gros') is-invalid @enderror" 
                                       id="stock_gros" name="stock_gros" value="{{ old('stock_gros', 0) }}">
                                @error('stock_gros')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section Prix et Stock Détail -->
                    <div id="section_detail" class="border p-3 mb-3 rounded" style="display: none;">
                        <h5 class="mb-3">Informations Détail</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prix_achat_detail" class="form-label">Prix d'achat Détail (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('prix_achat_detail') is-invalid @enderror" 
                                       id="prix_achat_detail" name="prix_achat_detail" value="{{ old('prix_achat_detail') }}">
                                @error('prix_achat_detail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="prix_vente_detail" class="form-label">Prix de vente Détail (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('prix_vente_detail') is-invalid @enderror" 
                                       id="prix_vente_detail" name="prix_vente_detail" value="{{ old('prix_vente_detail') }}">
                                @error('prix_vente_detail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock_detail" class="form-label">Stock Détail <span class="text-danger">*</span></label>
                                <input type="number" min="0" 
                                       class="form-control @error('stock_detail') is-invalid @enderror" 
                                       id="stock_detail" name="stock_detail" value="{{ old('stock_detail', 0) }}">
                                @error('stock_detail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Créer le produit</button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('type_produit').addEventListener('change', function() {
            const typeProduit = this.value;
            const sectionGros = document.getElementById('section_gros');
            const sectionDetail = document.getElementById('section_detail');
            const bulkUnitGroup = document.getElementById('bulk_unit_group');
            const conversionGroup = document.getElementById('conversion_group');
            
            // Réinitialiser les champs requis
            document.querySelectorAll('#section_gros input, #section_detail input').forEach(input => {
                input.required = false;
            });

            if (typeProduit === 'Gros') {
                sectionGros.style.display = 'block';
                sectionDetail.style.display = 'none';
                document.querySelectorAll('#section_gros input[type="number"]').forEach(input => {
                    input.required = true;
                });
            } else if (typeProduit === 'Détail') {
                sectionGros.style.display = 'none';
                sectionDetail.style.display = 'block';
                document.querySelectorAll('#section_detail input[type="number"]').forEach(input => {
                    input.required = true;
                });
            } else if (typeProduit === 'Les deux') {
                sectionGros.style.display = 'block';
                sectionDetail.style.display = 'block';
                document.querySelectorAll('#section_gros input[type="number"], #section_detail input[type="number"]').forEach(input => {
                    input.required = true;
                });
            } else {
                sectionGros.style.display = 'none';
                sectionDetail.style.display = 'none';
            }

            const displayBulk = typeProduit === 'Gros' || typeProduit === 'Les deux';
            bulkUnitGroup.style.display = displayBulk ? 'block' : 'none';
            conversionGroup.style.display = displayBulk ? 'block' : 'none';
            document.getElementById('bulk_unit_label').required = displayBulk;
            document.getElementById('units_per_bulk').required = displayBulk;
        });

        // Déclencher l'événement au chargement si une valeur existe
        const typeProduit = document.getElementById('type_produit').value;
        if (typeProduit) {
            document.getElementById('type_produit').dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
