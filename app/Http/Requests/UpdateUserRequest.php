<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request pour la modification d'un utilisateur
 * 
 * Validation robuste avec protection contre l'élévation de privilèges
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->route('user'); // Récupérer l'utilisateur depuis la route

        // Vérifier via la Policy si l'utilisateur peut modifier cet utilisateur
        return $this->user()->can('update', $user);
    }

    /**
     * Règles de validation
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : $user;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId), // Email unique sauf pour cet utilisateur
            ],
            'password' => [
                'nullable', // Mot de passe optionnel lors de la modification
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                'string',
                Rule::in(User::ROLES),
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
        // S'assurer que le rôle est en minuscules
        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower($this->role),
            ]);
        }

        // Si le mot de passe est vide, le retirer pour ne pas le modifier
        if ($this->has('password') && empty($this->password)) {
            $this->merge([
                'password' => null,
            ]);
        }
    }

    /**
     * Préparer les données validées pour l'utilisation
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Retirer le mot de passe si non fourni
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        return $validated;
    }
}


