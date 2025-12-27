<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request pour la création d'un utilisateur
 * 
 * Validation robuste avec protection contre l'élévation de privilèges
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Seuls les administrateurs peuvent créer des utilisateurs
        return $this->user()->can('create', User::class);
    }

    /**
     * Règles de validation
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email', // Email unique
            ],
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 caractères
                'confirmed', // Doit correspondre à password_confirmation
            ],
            'role' => [
                'required',
                'string',
                Rule::in(User::ROLES), // Seulement les rôles valides
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
        ];
    }

    /**
     * Préparer les données pour la validation
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // S'assurer que le rôle est en minuscules pour la cohérence
        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower($this->role),
            ]);
        }
    }
}


