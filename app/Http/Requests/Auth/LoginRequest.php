<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_type' => ['required', 'string', Rule::in(['admin', 'user'])],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function credentials(): array
    {
        return $this->only('email', 'password');
    }

    /**
     * Vérifier que l'utilisateur correspond au type sélectionné
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_type.required' => 'Veuillez sélectionner un type d\'utilisateur.',
            'user_type.in' => 'Le type d\'utilisateur sélectionné n\'est pas valide.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }
}





