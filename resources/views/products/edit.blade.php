@extends('layouts.app')

@section('title', 'Modifier ' . $product->libelle . ' - WEGA')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
    }
    .form-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Modifier le Produit</h1>
            <p class="page-subtitle">{{ $product->libelle }} ({{ $product->code_produit }})</p>
        </div>
        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <form action="{{ route('products.update', $product) }}" method="POST" id="productForm">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-title">Informations Générales</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="categorie" class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
                        <select class="form-select @error('categorie') is-invalid @enderror" id="categorie" name="categorie" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="Vin & Boissons" {{ old('categorie', $product->categorie) == 'Vin & Boissons' ? 'selected' : '' }}>Vin & Boissons</option>
                            <option value="Produits Alimentaires" {{ old('categorie', $product->categorie) == 'Produits Alimentaires' ? 'selected' : '' }}>Produits Alimentaires</option>
                            <option value="Produits de vitrine" {{ old('categorie', $product->categorie) == 'Produits de vitrine' ? 'selected' : '' }}>Produits de vitrine</option>
                            <option value="Fournitures scolaires" {{ old('categorie', $product->categorie) == 'Fournitures scolaires' ? 'selected' : '' }}>Fournitures scolaires</option>
                            <option value="Savons & Autres" {{ old('categorie', $product->categorie) == 'Savons & Autres' ? 'selected' : '' }}>Savons & Autres</option>
                            <option value="Biscuits & BonBon" {{ old('categorie', $product->categorie) == 'Biscuits & BonBon' ? 'selected' : '' }}>Biscuits & BonBon</option>
                        </select>
                        @error('categorie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="libelle" class="form-label fw-semibold">Libellé <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('libelle') is-invalid @enderror" id="libelle" name="libelle" value="{{ old('libelle', $product->libelle) }}" required>
                        @error('libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="type_produit" class="form-label fw-semibold">Type de produit <span class="text-danger">*</span></label>
                        <select class="form-select @error('type_produit') is-invalid @enderror" id="type_produit" name="type_produit" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="Gros" {{ old('type_produit', $product->type_produit) == 'Gros' ? 'selected' : '' }}>En Gros</option>
                            <option value="Détail" {{ old('type_produit', $product->type_produit) == 'Détail' ? 'selected' : '' }}>En Détail</option>
                            <option value="Les deux" {{ old('type_produit', $product->type_produit) == 'Les deux' ? 'selected' : '' }}>Les deux (Gros & Détail)</option>
                        </select>
                        @error('type_produit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="lieu" class="form-label fw-semibold">Lieu <span class="text-danger">*</span></label>
                        <select class="form-select @error('lieu') is-invalid @enderror" id="lieu" name="lieu" required>
                            <option value="">Sélectionnez un lieu</option>
                            <option value="Stock" {{ old('lieu', $product->lieu) == 'Stock' ? 'selected' : '' }}>Stock</option>
                            <option value="Boutique 1" {{ old('lieu', $product->lieu) == 'Boutique 1' ? 'selected' : '' }}>Boutique 1</option>
                            <option value="Boutique 2" {{ old('lieu', $product->lieu) == 'Boutique 2' ? 'selected' : '' }}>Boutique 2</option>
                        </select>
                        @error('lieu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">Unités de Mesure</div>
                <div class="row g-3">
                    <div class="col-md-4" id="bulk_unit_group">
                        <label for="bulk_unit_label" class="form-label fw-semibold">Unité gros</label>
                        <input type="text" class="form-control @error('bulk_unit_label') is-invalid @enderror" id="bulk_unit_label" name="bulk_unit_label" value="{{ old('bulk_unit_label', $product->bulk_unit_label ?? 'Carton') }}">
                        @error('bulk_unit_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="detail_unit_label" class="form-label fw-semibold">Unité détail <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('detail_unit_label') is-invalid @enderror" id="detail_unit_label" name="detail_unit_label" value="{{ old('detail_unit_label', $product->detail_unit_label ?? 'Pièce') }}" required>
                        @error('detail_unit_label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4" id="conversion_group">
                        <label for="units_per_bulk" class="form-label fw-semibold">Conversion (1 gros = ? détail)</label>
                        <input type="number" min="1" class="form-control @error('units_per_bulk') is-invalid @enderror" id="units_per_bulk" name="units_per_bulk" value="{{ old('units_per_bulk', $product->units_per_bulk ?? 1) }}">
                        @error('units_per_bulk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div id="section_gros" class="form-section" style="display: none;">
                <div class="form-section-title">Informations Gros</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="prix_achat_gros" class="form-label fw-semibold">Prix d'achat Gros (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('prix_achat_gros') is-invalid @enderror" id="prix_achat_gros" name="prix_achat_gros" value="{{ old('prix_achat_gros', $product->prix_achat_gros) }}">
                        @error('prix_achat_gros')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="prix_vente_gros" class="form-label fw-semibold">Prix de vente Gros (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('prix_vente_gros') is-invalid @enderror" id="prix_vente_gros" name="prix_vente_gros" value="{{ old('prix_vente_gros', $product->prix_vente_gros) }}">
                        @error('prix_vente_gros')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="stock_gros" class="form-label fw-semibold">Stock Gros <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control @error('stock_gros') is-invalid @enderror" id="stock_gros" name="stock_gros" value="{{ old('stock_gros', $product->stock_gros ?? 0) }}">
                        @error('stock_gros')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div id="section_detail" class="form-section" style="display: none;">
                <div class="form-section-title">Informations Détail</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="prix_achat_detail" class="form-label fw-semibold">Prix d'achat Détail (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('prix_achat_detail') is-invalid @enderror" id="prix_achat_detail" name="prix_achat_detail" value="{{ old('prix_achat_detail', $product->prix_achat_detail) }}">
                        @error('prix_achat_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="prix_vente_detail" class="form-label fw-semibold">Prix de vente Détail (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control @error('prix_vente_detail') is-invalid @enderror" id="prix_vente_detail" name="prix_vente_detail" value="{{ old('prix_vente_detail', $product->prix_vente_detail) }}">
                        @error('prix_vente_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="stock_detail" class="form-label fw-semibold">Stock Détail <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control @error('stock_detail') is-invalid @enderror" id="stock_detail" name="stock_detail" value="{{ old('stock_detail', $product->stock_detail ?? 0) }}">
                        @error('stock_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('type_produit').addEventListener('change', function() {
        const typeProduit = this.value;
        const sectionGros = document.getElementById('section_gros');
        const sectionDetail = document.getElementById('section_detail');
        const bulkUnitGroup = document.getElementById('bulk_unit_group');
        const conversionGroup = document.getElementById('conversion_group');
        
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

    const typeProduit = document.getElementById('type_produit').value;
    if (typeProduit) {
        document.getElementById('type_produit').dispatchEvent(new Event('change'));
    }
</script>
@endpush

