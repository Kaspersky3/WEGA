<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'inventory_date',
        'status',
        'year',
        'month',
        'month_name',
        'total_ca',
        'total_cost',
        'total_margin',
        'total_stock_theorique',
        'total_stock_reel',
        'total_gap_units',
        'total_gap_value',
        'notes',
        'filters_snapshot',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_ca' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_margin' => 'decimal:2',
        'inventory_date' => 'datetime',
        'total_stock_theorique' => 'integer',
        'total_stock_reel' => 'integer',
        'total_gap_units' => 'integer',
        'total_gap_value' => 'decimal:2',
        'filters_snapshot' => 'array',
    ];

    // Relations
    public function details()
    {
        return $this->hasMany(InventoryDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getMonthNameAttribute()
    {
        if (array_key_exists('month_name', $this->attributes) && $this->attributes['month_name']) {
            return $this->attributes['month_name'];
        }

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $months[$this->month] ?? '';
    }
}
