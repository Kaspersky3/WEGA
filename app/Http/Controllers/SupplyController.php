<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    // Afficher le formulaire d'approvisionnement
    public function create()
    {
        return view('supplies.create');
    }

    // Enregistrer un approvisionnement
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type_approvisionnement' => 'required|in:Gros,Détail,Les deux',
            'quantite_gros' => 'nullable|integer|min:0|required_if:type_approvisionnement,Gros|required_if:type_approvisionnement,Les deux',
            'quantite_detail' => 'nullable|integer|min:0|required_if:type_approvisionnement,Détail|required_if:type_approvisionnement,Les deux',
            'date_approvisionnement' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Création de l'approvisionnement
        $supply = Supply::create([
            'product_id' => $validated['product_id'],
            'type_approvisionnement' => $validated['type_approvisionnement'],
            'quantite_gros' => $validated['quantite_gros'] ?? 0,
            'quantite_detail' => $validated['quantite_detail'] ?? 0,
            'date_approvisionnement' => $validated['date_approvisionnement'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Mise à jour des stocks du produit
        $product = Product::findOrFail($validated['product_id']);
        
        if ($validated['type_approvisionnement'] == 'Gros' || $validated['type_approvisionnement'] == 'Les deux') {
            $product->stock_gros += $validated['quantite_gros'] ?? 0;
            $product->montant_total_achats += ($product->prix_achat_gros ?? 0) * ($validated['quantite_gros'] ?? 0);
        }
        
        if ($validated['type_approvisionnement'] == 'Détail' || $validated['type_approvisionnement'] == 'Les deux') {
            $product->stock_detail += $validated['quantite_detail'] ?? 0;
            $product->montant_total_achats += ($product->prix_achat_detail ?? 0) * ($validated['quantite_detail'] ?? 0);
        }
        
        // Mise à jour du stock actuel (en unités détail)
        $conversionRate = $product->conversionRate();
        $stockActuel = ($product->stock_gros ?? 0) * $conversionRate + ($product->stock_detail ?? 0);
        $product->stock_actuel = $stockActuel;
        
        $product->save();

        return redirect()->route('products.show', $product->id)->with('success', 'Approvisionnement enregistré avec succès !');
    }

    // API pour l'autocomplete
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('libelle', 'like', '%' . $query . '%')
            ->orWhere('code_produit', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'code_produit', 'libelle', 'categorie']);

        return response()->json($products);
    }
}
