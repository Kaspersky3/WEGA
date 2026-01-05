<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'product_id',
        'lieu_source',
        'lieu_destination',
        'quantite_gros',
        'quantite_detail',
        'date_transfert',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantite_gros' => 'integer',
        'quantite_detail' => 'integer',
        'date_transfert' => 'datetime',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
