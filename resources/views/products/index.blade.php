<!DOCTYPE html>
<html>
<head>
    <title>Liste des produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Liste des produits</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('inventories.index') }}" class="btn btn-warning">Inventaires</a>
                <a href="{{ route('supplies.create') }}" class="btn btn-success">+ Approvisionnement</a>
                <a href="{{ route('products.create') }}" class="btn btn-primary">+ Ajouter un produit</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Barre de recherche -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="searchForm" method="GET" action="{{ route('products.index') }}">
                    <div class="row g-3">
                        <div class="col-md-10">
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Rechercher par libellé, code produit ou catégorie..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                        </div>
                    </div>
                    @if(request('search'))
                        <div class="mt-2">
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">Effacer la recherche</a>
                            <span class="ms-2 text-muted">Résultats: <strong id="resultCount">{{ $products->count() }}</strong> produit(s)</span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tableau des produits -->
        <div id="productsTable">
            @include('products.partials.table', ['products' => $products])
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Recherche en temps réel (AJAX)
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
                    // Si le champ est vide, charger tous les produits
                    searchTimeout = setTimeout(() => {
                        window.location.href = '{{ route("products.index") }}';
                    }, 500);
                    return;
                }

                // Recherche après 300ms d'inactivité
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
                        // Mettre à jour l'URL sans recharger la page
                        const url = new URL(window.location);
                        url.searchParams.set('search', query);
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                    });
                }, 300);
            });

            // Permettre la recherche avec Entrée
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
</body>
</html>

