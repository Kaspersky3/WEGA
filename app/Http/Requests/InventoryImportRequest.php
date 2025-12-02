<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'], // 10MB max
            'inventory_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Veuillez sélectionner un fichier CSV ou Excel.',
            'csv_file.file' => 'Le fichier sélectionné n\'est pas valide.',
            'csv_file.mimes' => 'Le fichier doit être au format CSV ou Excel (.csv, .txt, .xlsx).',
            'csv_file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'inventory_date.required' => 'La date d\'inventaire est requise.',
            'inventory_date.date' => 'La date d\'inventaire doit être une date valide.',
        ];
    }
}

