@extends('layouts.app')

@section('title', 'Produits - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">Gestion des Produits</h1>
            <p class="page-subtitle">Consultez et gérez votre catalogue de produits</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clipboard-check"></i> Inventaires
            </a>
            <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left-right"></i> Transferts
            </a>
            <a href="{{ route('supplies.create') }}" class="btn btn-success">
                <i class="bi bi-cart-plus"></i> Approvisionnement
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau Produit
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form id="searchForm" method="GET" action="{{ route('products.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-10">
                    <label for="search" class="form-label fw-semibold">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               placeholder="Rechercher par libellé, code produit ou catégorie..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </div>
            @if(request('search'))
                <div class="mt-3 d-flex align-items-center gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Effacer
                    </a>
                    <span class="text-muted small">
                        <strong id="resultCount">{{ $products->count() }}</strong> résultat(s) trouvé(s)
                    </span>
                </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div id="productsTable">
            @include('products.partials.table', ['products' => $products])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let searchTimeout;
    const searchInput = document.getElementById('search');
    const productsTable = document.getElementById('productsTable');
    const resultCount = document.getElementById('resultCount');
    const searchForm = document.getElementById('searchForm');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length === 0) {
                searchTimeout = setTimeout(() => {
                    window.location.href = '{{ route("products.index") }}';
                }, 500);
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('products.index') }}?search=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    productsTable.innerHTML = data.html;
                    if (resultCount) {
                        resultCount.textContent = data.count;
                    }
                    const url = new URL(window.location);
                    url.searchParams.set('search', query);
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                });
            }, 300);
        });

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = `{{ route('products.index') }}?search=${encodeURIComponent(query)}`;
            } else {
                window.location.href = '{{ route('products.index') }}';
            }
        });
    }
</script>
@endpush
