<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'categorie' => ['required', 'string', 'max:255'],
            'libelle' => ['required', 'string', 'max:255'],
            'type_produit' => ['required', 'in:Gros,Détail,Les deux'],
            'lieu' => ['required', 'in:Stock,Boutique 1,Boutique 2'],
            'detail_unit_label' => ['required', 'string', 'max:50'],
        ];

        if ($this->type_produit === 'Gros' || $this->type_produit === 'Les deux') {
            $rules['bulk_unit_label'] = ['required', 'string', 'max:50'];
            $rules['units_per_bulk'] = ['required', 'integer', 'min:1'];
        } else {
            $rules['bulk_unit_label'] = ['nullable', 'string', 'max:50'];
            $rules['units_per_bulk'] = ['nullable', 'integer', 'min:1'];
        }

        if ($this->type_produit == 'Gros' || $this->type_produit == 'Les deux') {
            $rules['prix_achat_gros'] = ['required', 'numeric', 'min:0'];
            $rules['prix_vente_gros'] = ['required', 'numeric', 'min:0'];
            $rules['stock_gros'] = ['required', 'integer', 'min:0'];
        }

        if ($this->type_produit == 'Détail' || $this->type_produit == 'Les deux') {
            $rules['prix_achat_detail'] = ['required', 'numeric', 'min:0'];
            $rules['prix_vente_detail'] = ['required', 'numeric', 'min:0'];
            $rules['stock_detail'] = ['required', 'integer', 'min:0'];
        }

        return $rules;
    }
}

