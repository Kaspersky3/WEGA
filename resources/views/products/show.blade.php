<!DOCTYPE html>
<html>
<head>
    <title>Fiche détaillée - {{ $product->libelle }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Fiche détaillée du produit</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('supplies.create') }}" class="btn btn-success">+ Approvisionnement</a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Informations générales -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Code Produit:</th>
                                <td><strong>{{ $product->code_produit }}</strong></td>
                            </tr>
                            <tr>
                                <th>Libellé:</th>
                                <td>{{ $product->libelle }}</td>
                            </tr>
                            <tr>
                                <th>Catégorie:</th>
                                <td>{{ $product->categorie }}</td>
                            </tr>
                            <tr>
                                <th>Type de produit:</th>
                                <td>
                                    @if($product->type_produit)
                                        <span class="badge bg-info">{{ $product->type_produit }}</span>
                                    @else
                                        <span class="badge bg-secondary">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Lieu:</th>
                                <td>{{ $product->lieu }}</td>
                            </tr>
                            <tr>
                                <th>Date d'enregistrement:</th>
                                <td>{{ $product->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Informations de stock -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Informations de stock</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
                            <tr>
                                <th width="50%">Stock Gros:</th>
                                <td><span class="badge bg-primary fs-6">{{ $product->stock_gros ?? 0 }}</span></td>
                            </tr>
                            @endif
                            @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
                            <tr>
                                <th>Stock Détail:</th>
                                <td><span class="badge bg-success fs-6">{{ $product->stock_detail ?? 0 }}</span></td>
                            </tr>
                            @endif
                            @if($product->type_produit == 'Les deux')
                            <tr>
                                <th>Stock Total:</th>
                                <td><strong>{{ ($product->stock_gros ?? 0) + ($product->stock_detail ?? 0) }}</strong></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Prix Gros -->
            @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Prix Gros (FCFA)</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="50%">Prix d'achat Gros:</th>
                                <td><strong>{{ number_format($product->prix_achat_gros ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <th>Prix de vente Gros:</th>
                                <td><strong>{{ number_format($product->prix_vente_gros ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Prix Détail -->
            @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Prix Détail (FCFA)</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="50%">Prix d'achat Détail:</th>
                                <td><strong>{{ number_format($product->prix_achat_detail ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            <tr>
                                <th>Prix de vente Détail:</th>
                                <td><strong>{{ number_format($product->prix_vente_detail ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Dernier approvisionnement -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Dernier approvisionnement</h5>
                    </div>
                    <div class="card-body">
                        @if($dernierApprovisionnement)
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="20%">Date:</th>
                                    <td>{{ \Carbon\Carbon::parse($dernierApprovisionnement->date_approvisionnement)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td><span class="badge bg-primary">{{ $dernierApprovisionnement->type_approvisionnement }}</span></td>
                                </tr>
                                @if($dernierApprovisionnement->type_approvisionnement == 'Gros' || $dernierApprovisionnement->type_approvisionnement == 'Les deux')
                                <tr>
                                    <th>Quantité Gros:</th>
                                    <td>{{ $dernierApprovisionnement->quantite_gros }}</td>
                                </tr>
                                @endif
                                @if($dernierApprovisionnement->type_approvisionnement == 'Détail' || $dernierApprovisionnement->type_approvisionnement == 'Les deux')
                                <tr>
                                    <th>Quantité Détail:</th>
                                    <td>{{ $dernierApprovisionnement->quantite_detail }}</td>
                                </tr>
                                @endif
                            </table>
                        @else
                            <p class="text-muted mb-0">Aucun approvisionnement enregistré</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques de ventes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Statistiques de ventes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6>Quantités vendues</h6>
                                <table class="table table-sm table-borderless">
                                    @if($product->type_produit == 'Gros' || $product->type_produit == 'Les deux')
                                    <tr>
                                        <th width="60%">Quantité vendue Gros:</th>
                                        <td><strong>{{ $product->quantite_vendue_gros ?? 0 }}</strong></td>
                                    </tr>
                                    @endif
                                    @if($product->type_produit == 'Détail' || $product->type_produit == 'Les deux')
                                    <tr>
                                        <th>Quantité vendue Détail:</th>
                                        <td><strong>{{ $product->quantite_vendue_detail ?? 0 }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr class="border-top">
                                        <th>Quantité totale vendue:</th>
                                        <td><strong>{{ ($product->quantite_vendue_gros ?? 0) + ($product->quantite_vendue_detail ?? 0) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>Montants</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="60%">Montant total des ventes:</th>
                                        <td><strong class="text-success">{{ number_format($product->montant_total_ventes ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Montant total des achats:</th>
                                        <td><strong class="text-danger">{{ number_format($product->montant_total_achats ?? 0, 0, ',', ' ') }} FCFA</strong></td>
                                    </tr>
                                    <tr class="border-top">
                                        <th>Marge bénéficiaire:</th>
                                        <td>
                                            <strong class="{{ $margeBeneficiaire >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($margeBeneficiaire, 0, ',', ' ') }} FCFA
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique des approvisionnements -->
        @if($product->supplies->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Historique des approvisionnements</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Quantité Gros</th>
                                        <th>Quantité Détail</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->supplies->sortByDesc('date_approvisionnement') as $supply)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($supply->date_approvisionnement)->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-primary">{{ $supply->type_approvisionnement }}</span></td>
                                            <td>{{ $supply->quantite_gros ?? 0 }}</td>
                                            <td>{{ $supply->quantite_detail ?? 0 }}</td>
                                            <td>{{ $supply->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






