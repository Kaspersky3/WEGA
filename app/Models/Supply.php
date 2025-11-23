<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type_approvisionnement',
        'quantite_gros',
        'quantite_detail',
        'date_approvisionnement',
        'notes',
    ];

    protected $casts = [
        'quantite_gros' => 'integer',
        'quantite_detail' => 'integer',
        'date_approvisionnement' => 'date',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
