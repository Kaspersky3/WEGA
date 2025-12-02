<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Afficher la liste des produits
    public function index(Request $request)
    {
        $query = Product::query();

        // Recherche par libellé et catégorie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('libelle', 'like', '%' . $search . '%')
                  ->orWhere('code_produit', 'like', '%' . $search . '%')
                  ->orWhere('categorie', 'like', '%' . $search . '%');
            });
        }

        $products = $query->orderBy('libelle')->get();
        
        // Si requête AJAX, retourner JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.table', compact('products'))->render(),
                'count' => $products->count()
            ]);
        }

        return view('products.index', compact('products'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        return view('products.create');
    }

    // Enregistrer un nouveau produit
    public function store(Request $request)
    {
        // Validation des données selon le type de produit
        $rules = [
            'categorie' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'type_produit' => 'required|in:Gros,Détail,Les deux',
            'lieu' => 'required|in:Stock,Boutique',
            'detail_unit_label' => 'required|string|max:50',
        ];

        if ($request->type_produit === 'Gros' || $request->type_produit === 'Les deux') {
            $rules['bulk_unit_label'] = 'required|string|max:50';
            $rules['units_per_bulk'] = 'required|integer|min:1';
        } else {
            $rules['bulk_unit_label'] = 'nullable|string|max:50';
            $rules['units_per_bulk'] = 'nullable|integer|min:1';
        }

        // Validation conditionnelle selon le type
        if ($request->type_produit == 'Gros' || $request->type_produit == 'Les deux') {
            $rules['prix_achat_gros'] = 'required|numeric|min:0';
            $rules['prix_vente_gros'] = 'required|numeric|min:0';
            $rules['stock_gros'] = 'required|integer|min:0';
        }

        if ($request->type_produit == 'Détail' || $request->type_produit == 'Les deux') {
            $rules['prix_achat_detail'] = 'required|numeric|min:0';
            $rules['prix_vente_detail'] = 'required|numeric|min:0';
            $rules['stock_detail'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        // Préparation des données pour la création
        $productData = [
            'categorie' => $validated['categorie'],
            'libelle' => $validated['libelle'],
            'type_produit' => $validated['type_produit'],
            'lieu' => $validated['lieu'],
            'bulk_unit_label' => $validated['bulk_unit_label'] ?? 'Carton',
            'detail_unit_label' => $validated['detail_unit_label'],
            'units_per_bulk' => $validated['units_per_bulk'] ?? 1,
        ];

        // Ajout des prix et stocks selon le type
        if ($request->type_produit == 'Gros' || $request->type_produit == 'Les deux') {
            $productData['prix_achat_gros'] = $validated['prix_achat_gros'];
            $productData['prix_vente_gros'] = $validated['prix_vente_gros'];
            $productData['stock_gros'] = $validated['stock_gros'];
            
            // Pour compatibilité avec les anciens champs, on utilise les prix gros
            $productData['prix_achat'] = $validated['prix_achat_gros'];
            $productData['prix_vente'] = $validated['prix_vente_gros'];
        }

        if ($request->type_produit == 'Détail' || $request->type_produit == 'Les deux') {
            $productData['prix_achat_detail'] = $validated['prix_achat_detail'];
            $productData['prix_vente_detail'] = $validated['prix_vente_detail'];
            $productData['stock_detail'] = $validated['stock_detail'];
            
            // Pour compatibilité avec les anciens champs, on utilise les prix détail
            // Si c'est "Les deux", on garde les prix gros (déjà définis)
            if ($request->type_produit == 'Détail') {
                $productData['prix_achat'] = $validated['prix_achat_detail'];
                $productData['prix_vente'] = $validated['prix_vente_detail'];
            }
        }

        // Stocks initiaux pour compatibilité (peuvent être 0 ou calculés)
        $productData['stock_initial'] = 0;
        if ($request->type_produit == 'Gros' || $request->type_produit == 'Les deux') {
            $productData['stock_initial'] += $validated['stock_gros'] ?? 0;
        }
        if ($request->type_produit == 'Détail' || $request->type_produit == 'Les deux') {
            $productData['stock_initial'] += $validated['stock_detail'] ?? 0;
        }
        
        $productData['stock_actuel'] = $productData['stock_initial'];

        // Création du produit (le code_produit sera généré automatiquement)
        Product::create($productData);

        return redirect()->route('products.index')->with('success', 'Produit créé avec succès !');
    }

    // Afficher la fiche détaillée d'un produit
    public function show($id)
    {
        $product = Product::with(['supplies' => function($query) {
            $query->latest('date_approvisionnement');
        }, 'sales' => function($query) {
            $query->latest('date_vente');
        }])->findOrFail($id);

        // Calcul de la marge bénéficiaire
        $margeBeneficiaire = $product->montant_total_ventes - $product->montant_total_achats;

        // Dernier approvisionnement
        $dernierApprovisionnement = $product->supplies()->latest('date_approvisionnement')->first();

        return view('products.show', compact('product', 'margeBeneficiaire', 'dernierApprovisionnement'));
    }

    // Afficher le formulaire d'édition
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Mettre à jour un produit
    public function update(ProductUpdateRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validated();

        // Préparation des données pour la mise à jour
        $productData = [
            'categorie' => $validated['categorie'],
            'libelle' => $validated['libelle'],
            'type_produit' => $validated['type_produit'],
            'lieu' => $validated['lieu'],
            'detail_unit_label' => $validated['detail_unit_label'],
        ];

        // Unités de mesure
        if ($request->type_produit === 'Gros' || $request->type_produit === 'Les deux') {
            $productData['bulk_unit_label'] = $validated['bulk_unit_label'];
            $productData['units_per_bulk'] = $validated['units_per_bulk'];
        } else {
            $productData['bulk_unit_label'] = null;
            $productData['units_per_bulk'] = null;
        }

        // Prix et stocks selon le type
        if ($request->type_produit == 'Gros' || $request->type_produit == 'Les deux') {
            $productData['prix_achat_gros'] = $validated['prix_achat_gros'];
            $productData['prix_vente_gros'] = $validated['prix_vente_gros'];
            $productData['stock_gros'] = $validated['stock_gros'];
            $productData['prix_achat'] = $validated['prix_achat_gros'];
            $productData['prix_vente'] = $validated['prix_vente_gros'];
        } else {
            $productData['prix_achat_gros'] = null;
            $productData['prix_vente_gros'] = null;
            $productData['stock_gros'] = null;
        }

        if ($request->type_produit == 'Détail' || $request->type_produit == 'Les deux') {
            $productData['prix_achat_detail'] = $validated['prix_achat_detail'];
            $productData['prix_vente_detail'] = $validated['prix_vente_detail'];
            $productData['stock_detail'] = $validated['stock_detail'];
            
            if ($request->type_produit == 'Détail') {
                $productData['prix_achat'] = $validated['prix_achat_detail'];
                $productData['prix_vente'] = $validated['prix_vente_detail'];
            }
        } else {
            $productData['prix_achat_detail'] = null;
            $productData['prix_vente_detail'] = null;
            $productData['stock_detail'] = null;
        }

        // Calcul du stock actuel (en unités détail)
        $stockActuel = 0;
        if ($request->type_produit == 'Gros' || $request->type_produit == 'Les deux') {
            $stockActuel += ($validated['stock_gros'] ?? 0) * ($productData['units_per_bulk'] ?? 1);
        }
        if ($request->type_produit == 'Détail' || $request->type_produit == 'Les deux') {
            $stockActuel += ($validated['stock_detail'] ?? 0);
        }
        $productData['stock_actuel'] = $stockActuel;
        
        // Mise à jour du stock initial pour cohérence
        $productData['stock_initial'] = $stockActuel;

        $product->update($productData);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit modifié avec succès !');
    }

    // Supprimer un produit
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Vérifier s'il y a des approvisionnements ou ventes associés
        $hasSupplies = $product->supplies()->exists();
        $hasSales = $product->sales()->exists();

        if ($hasSupplies || $hasSales) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'Impossible de supprimer ce produit car il possède des approvisionnements ou des ventes associés.');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé avec succès !');
    }

    // Méthode helper pour préparer les données du produit
    protected function prepareProductData(array $validated, string $typeProduit): array
    {
        $productData = [
            'categorie' => $validated['categorie'],
            'libelle' => $validated['libelle'],
            'type_produit' => $typeProduit,
            'lieu' => $validated['lieu'],
            'detail_unit_label' => $validated['detail_unit_label'],
        ];

        if ($typeProduit === 'Gros' || $typeProduit === 'Les deux') {
            $productData['bulk_unit_label'] = $validated['bulk_unit_label'];
            $productData['units_per_bulk'] = $validated['units_per_bulk'];
        }

        if ($typeProduit == 'Gros' || $typeProduit == 'Les deux') {
            $productData['prix_achat_gros'] = $validated['prix_achat_gros'];
            $productData['prix_vente_gros'] = $validated['prix_vente_gros'];
            $productData['stock_gros'] = $validated['stock_gros'];
            $productData['prix_achat'] = $validated['prix_achat_gros'];
            $productData['prix_vente'] = $validated['prix_vente_gros'];
        }

        if ($typeProduit == 'Détail' || $typeProduit == 'Les deux') {
            $productData['prix_achat_detail'] = $validated['prix_achat_detail'];
            $productData['prix_vente_detail'] = $validated['prix_vente_detail'];
            $productData['stock_detail'] = $validated['stock_detail'];
            
            if ($typeProduit == 'Détail') {
                $productData['prix_achat'] = $validated['prix_achat_detail'];
                $productData['prix_vente'] = $validated['prix_vente_detail'];
            }
        }

        return $productData;
    }
}
