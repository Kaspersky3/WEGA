<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'lieu_source' => ['required', 'in:Stock,Boutique 1,Boutique 2'],
            'lieu_destination' => ['required', 'in:Stock,Boutique 1,Boutique 2', 'different:lieu_source'],
            'quantite_gros' => ['nullable', 'integer', 'min:0'],
            'quantite_detail' => ['nullable', 'integer', 'min:0'],
            'date_transfert' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire.',
            'product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'lieu_source.required' => 'Le lieu source est obligatoire.',
            'lieu_source.in' => 'Le lieu source doit être Stock, Boutique 1 ou Boutique 2.',
            'lieu_destination.required' => 'Le lieu de destination est obligatoire.',
            'lieu_destination.in' => 'Le lieu de destination doit être Stock, Boutique 1 ou Boutique 2.',
            'lieu_destination.different' => 'Le lieu de destination doit être différent du lieu source.',
            'quantite_gros.integer' => 'La quantité en gros doit être un nombre entier.',
            'quantite_gros.min' => 'La quantité en gros doit être positive ou nulle.',
            'quantite_detail.integer' => 'La quantité en détail doit être un nombre entier.',
            'quantite_detail.min' => 'La quantité en détail doit être positive ou nulle.',
            'date_transfert.required' => 'La date de transfert est obligatoire.',
            'date_transfert.date' => 'La date de transfert doit être une date valide.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $quantiteGros = (int) ($this->input('quantite_gros') ?? 0);
            $quantiteDetail = (int) ($this->input('quantite_detail') ?? 0);

            if ($quantiteGros === 0 && $quantiteDetail === 0) {
                $validator->errors()->add(
                    'quantite_gros',
                    'Vous devez spécifier au moins une quantité (gros ou détail) à transférer.'
                );
            }
        });
    }
}
