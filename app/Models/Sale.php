<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type_vente',
        'quantite',
        'prix_unitaire',
        'montant_total',
        'date_vente',
        'notes',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'date_vente' => 'date',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
