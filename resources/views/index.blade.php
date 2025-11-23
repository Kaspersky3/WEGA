<!DOCTYPE html>
<html>
<head>
    <title>Liste des produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h1 class="mb-4">Liste des produits</h1>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Catégorie</th>
                    <th>Libellé</th>
                    <th>Prix Achat</th>
                    <th>Prix Vente</th>
                    <th>Stock Initial</th>
                    <th>Stock Actuel</th>
                    <th>Lieu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->categorie }}</td>
                        <td>{{ $product->libelle }}</td>
                        <td>{{ $product->prix_achat }}</td>
                        <td>{{ $product->prix_vente }}</td>
                        <td>{{ $product->stock_initial }}</td>
                        <td>{{ $product->stock_actuel }}</td>
                        <td>{{ $product->lieu }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
