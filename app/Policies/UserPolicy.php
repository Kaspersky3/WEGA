<?php

namespace App\Policies;

use App\Models\User;

/**
 * Policy pour gérer les autorisations liées aux utilisateurs
 * 
 * Cette policy contrôle qui peut créer, modifier, supprimer et voir les utilisateurs.
 * Seuls les administrateurs peuvent gérer les utilisateurs.
 */
class UserPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des utilisateurs
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Déterminer si l'utilisateur peut voir un utilisateur spécifique
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        // Un admin peut voir n'importe quel utilisateur
        // Un utilisateur peut voir son propre profil
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Déterminer si l'utilisateur peut créer des utilisateurs
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Déterminer si l'utilisateur peut modifier un utilisateur
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        // Un admin peut modifier n'importe quel utilisateur
        // Un utilisateur peut modifier son propre profil (mais pas son rôle)
        if ($user->isAdmin()) {
            return true;
        }

        // Un utilisateur peut modifier son propre profil
        if ($user->id === $model->id) {
            return true;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un utilisateur
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        // Seuls les admins peuvent supprimer des utilisateurs
        // Un admin ne peut pas se supprimer lui-même (sécurité)
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Déterminer si l'utilisateur peut restaurer un utilisateur
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Déterminer si l'utilisateur peut supprimer définitivement un utilisateur
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Seuls les admins peuvent supprimer définitivement
        // Un admin ne peut pas se supprimer lui-même
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Déterminer si l'utilisateur peut modifier le rôle d'un utilisateur
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function updateRole(User $user, User $model): bool
    {
        // Seuls les admins peuvent modifier les rôles
        // Un admin ne peut pas modifier son propre rôle (sécurité)
        return $user->isAdmin() && $user->id !== $model->id;
    }
}

