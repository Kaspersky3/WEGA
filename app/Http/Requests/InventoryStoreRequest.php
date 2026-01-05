<?php

namespace App\Http\Requests;

use App\Models\Inventory;
use Illuminate\Foundation\Http\FormRequest;

class InventoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventory_date' => ['required', 'date'],
            'lieu' => ['required', 'in:Stock,Boutique 1,Boutique 2'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'filters.categorie' => ['nullable', 'string', 'max:255'],
            'filters.search' => ['nullable', 'string', 'max:255'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.stock_reel_gros' => ['nullable', 'integer', 'min:0'],
            'products.*.stock_reel_detail' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

