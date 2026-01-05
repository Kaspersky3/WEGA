<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_produit',
        'categorie',
        'libelle',
        'prix_achat',
        'prix_vente',
        'stock_initial',
        'stock_actuel',
        'lieu',
        'type_produit',
        'stock_gros',
        'stock_detail',
        'stock_boutique_gros',
        'stock_boutique_detail',
        'prix_achat_gros',
        'prix_vente_gros',
        'prix_achat_detail',
        'prix_vente_detail',
        'bulk_unit_label',
        'detail_unit_label',
        'units_per_bulk',
        'quantite_vendue_gros',
        'quantite_vendue_detail',
        'montant_total_ventes',
        'montant_total_achats',
    ];

    protected static function boot()
    {
        parent::boot();

        // Avant de créer un produit, on génère le code
        static::creating(function ($product) {
            // Récupère le dernier produit créé
            $lastProduct = Product::latest('id')->first();

            // Calcule le prochain numéro
            $nextNumber = $lastProduct ? ((int) substr($lastProduct->code_produit, 1)) + 1 : 1;

            // Formate le code avec 3 chiffres (ex: P001)
            $product->code_produit = 'P' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'stock_initial' => 'integer',
        'stock_actuel' => 'integer',
        'stock_gros' => 'integer',
        'stock_detail' => 'integer',
        'stock_boutique_gros' => 'integer',
        'stock_boutique_detail' => 'integer',
        'prix_achat_gros' => 'decimal:2',
        'prix_vente_gros' => 'decimal:2',
        'prix_achat_detail' => 'decimal:2',
        'prix_vente_detail' => 'decimal:2',
        'units_per_bulk' => 'integer',
        'quantite_vendue_gros' => 'integer',
        'quantite_vendue_detail' => 'integer',
        'montant_total_ventes' => 'decimal:2',
        'montant_total_achats' => 'decimal:2',
    ];

    // Relations
    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    // Méthodes utilitaires
    public function getLastSupplyDateAttribute()
    {
        $lastSupply = $this->supplies()->latest('date_approvisionnement')->first();
        return $lastSupply ? $lastSupply->date_approvisionnement : null;
    }

    public function getMargeBeneficiaireAttribute()
    {
        return $this->montant_total_ventes - $this->montant_total_achats;
    }

    // Helpers pour les inventaires
    /**
     * Récupère le stock initial à une date donnée (début du mois)
     */
    public function getStockInitialForDate($year, $month, $type = 'both')
    {
        $date = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        
        // Stock au début du mois = stock actuel - approvisionnements du mois + ventes supposées
        // Pour simplifier, on prend le stock actuel moins les approvisionnements du mois
        $suppliesMonth = $this->supplies()
            ->whereYear('date_approvisionnement', $year)
            ->whereMonth('date_approvisionnement', $month)
            ->get();

        $stockInitialGros = $this->stock_gros ?? 0;
        $stockInitialDetail = $this->stock_detail ?? 0;

        // On soustrait les approvisionnements du mois pour avoir le stock initial
        foreach ($suppliesMonth as $supply) {
            if ($supply->type_approvisionnement == 'Gros' || $supply->type_approvisionnement == 'Les deux') {
                $stockInitialGros -= ($supply->quantite_gros ?? 0);
            }
            if ($supply->type_approvisionnement == 'Détail' || $supply->type_approvisionnement == 'Les deux') {
                $stockInitialDetail -= ($supply->quantite_detail ?? 0);
            }
        }

        if ($type === 'gros') {
            return $stockInitialGros;
        } elseif ($type === 'detail') {
            return $stockInitialDetail;
        }

        return [
            'gros' => $stockInitialGros,
            'detail' => $stockInitialDetail
        ];
    }

    /**
     * Récupère la quantité approvisionnée sur une période
     */
    public function getApprovisionnementForPeriod($year, $month, $type = 'both')
    {
        $supplies = $this->supplies()
            ->whereYear('date_approvisionnement', $year)
            ->whereMonth('date_approvisionnement', $month)
            ->get();

        $qtyGros = 0;
        $qtyDetail = 0;

        foreach ($supplies as $supply) {
            if ($supply->type_approvisionnement == 'Gros' || $supply->type_approvisionnement == 'Les deux') {
                $qtyGros += ($supply->quantite_gros ?? 0);
            }
            if ($supply->type_approvisionnement == 'Détail' || $supply->type_approvisionnement == 'Les deux') {
                $qtyDetail += ($supply->quantite_detail ?? 0);
            }
        }

        if ($type === 'gros') {
            return $qtyGros;
        } elseif ($type === 'detail') {
            return $qtyDetail;
        }

        return [
            'gros' => $qtyGros,
            'detail' => $qtyDetail
        ];
    }

    public function conversionRate(): int
    {
        return max(1, (int) ($this->units_per_bulk ?? 1));
    }

    public function unitPurchasePrice(string $channel = 'detail'): float
    {
        if ($channel === 'gros' && $this->prix_achat_gros) {
            return (float) $this->prix_achat_gros;
        }

        if ($this->prix_achat_detail) {
            return (float) $this->prix_achat_detail;
        }

        return (float) ($this->prix_achat ?? 0);
    }
}
