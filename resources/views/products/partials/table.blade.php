@if(count($products) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Code Produit</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Type</th>
                    <th>Stock Gros</th>
                    <th>Stock Détail</th>
                    <th>Lieu</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->code_produit }}</td>
                        <td>{{ $product->libelle }}</td>
                        <td>{{ $product->categorie }}</td>
                        <td>
                            @if($product->type_produit)
                                <span class="badge bg-info">{{ $product->type_produit }}</span>
                            @else
                                <span class="badge bg-secondary">Non défini</span>
                            @endif
                        </td>
                        <td>
                            @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
                                <span class="badge bg-primary">{{ $product->stock_gros ?? 0 }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
                                <span class="badge bg-success">{{ $product->stock_detail ?? 0 }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $product->lieu }}</td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info">Voir détails</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info" role="alert">
        @if(request('search'))
            Aucun produit trouvé pour la recherche "{{ request('search') }}".
        @else
            Aucun produit trouvé dans la base de données.
        @endif
    </div>
@endif




