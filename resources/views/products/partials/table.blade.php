@if(count($products) > 0)
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Type</th>
                    <th>Stock Gros</th>
                    <th>Stock Détail</th>
                    <th>Lieu</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $product->code_produit }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $product->libelle }}</div>
                        </td>
                        <td>
                            <span class="text-muted">{{ $product->categorie }}</span>
                        </td>
                        <td>
                            @if($product->type_produit)
                                @if($product->type_produit == 'Gros')
                                    <span class="badge bg-info">Gros</span>
                                @elseif($product->type_produit == 'Détail')
                                    <span class="badge bg-success">Détail</span>
                                @else
                                    <span class="badge bg-secondary">Les deux</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Non défini</span>
                            @endif
                        </td>
                        <td>
                            @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
                                <span class="badge bg-primary">{{ number_format($product->stock_gros ?? 0, 0, ',', ' ') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
                                <span class="badge bg-success">{{ number_format($product->stock_detail ?? 0, 0, ',', ' ') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $product->lieu }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Voir les détails">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
        </div>
        <h5 class="text-muted">Aucun produit trouvé</h5>
        <p class="text-muted mb-4">
            @if(request('search'))
                Aucun produit ne correspond à votre recherche "{{ request('search') }}".
            @else
                Commencez par créer votre premier produit.
            @endif
        </p>
        @if(!request('search'))
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Créer un produit
            </a>
        @endif
    </div>
@endif
