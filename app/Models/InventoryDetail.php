<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'stock_theorique_gros',
        'stock_theorique_detail',
        'stock_theorique_total',
        'stock_reel_gros',
        'stock_reel_detail',
        'stock_reel_total',
        'conversion_rate',
        'gap_units',
        'gap_value',
        'unit_purchase_price',
    ];

    protected $casts = [
        'stock_theorique_gros' => 'integer',
        'stock_theorique_detail' => 'integer',
        'stock_theorique_total' => 'integer',
        'stock_reel_gros' => 'integer',
        'stock_reel_detail' => 'integer',
        'stock_reel_total' => 'integer',
        'conversion_rate' => 'integer',
        'gap_units' => 'integer',
        'gap_value' => 'decimal:2',
        'unit_purchase_price' => 'decimal:2',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getGapAbsoluteAttribute(): int
    {
        return abs($this->gap_units);
    }
}





