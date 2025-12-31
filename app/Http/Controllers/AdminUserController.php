<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Contrôleur pour la gestion des utilisateurs par les administrateurs
 * 
 * Toutes les méthodes sont protégées par :
 * - Middleware 'auth' : Authentification requise
 * - Middleware 'admin' : Rôle admin requis
 * - Policies : Vérification fine des permissions
 */
class AdminUserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // Vérifier la permission via Policy
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtre par rôle
        if ($request->filled('role') && User::isValidRole($request->role)) {
            $query->where('role', $request->role);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'role', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        // Vérifier la permission via Policy
        $this->authorize('create', User::class);

        return view('admin.users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        // La permission est vérifiée dans StoreUserRequest::authorize()

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Log de sécurité
        \Log::info('Nouvel utilisateur créé par un administrateur', [
            'created_by' => auth()->id(),
            'created_user_id' => $user->id,
            'created_user_email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->name} a été créé avec succès.");
    }

    /**
     * Afficher les détails d'un utilisateur
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user): View
    {
        // Vérifier la permission via Policy
        $this->authorize('view', $user);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user): View
    {
        // Vérifier la permission via Policy
        $this->authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // La permission est vérifiée dans UpdateUserRequest::authorize()

        // Préparer les données de mise à jour
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Mettre à jour le mot de passe seulement s'il est fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Vérifier si le rôle a changé (sécurité)
        $roleChanged = $user->role !== $request->role;

        $user->update($data);

        // Log de sécurité si le rôle a changé
        if ($roleChanged) {
            \Log::warning('Rôle utilisateur modifié par un administrateur', [
                'modified_by' => auth()->id(),
                'user_id' => $user->id,
                'old_role' => $user->getOriginal('role'),
                'new_role' => $user->role,
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->name} a été modifié avec succès.");
    }

    /**
     * Supprimer un utilisateur
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        // Vérifier la permission via Policy
        $this->authorize('delete', $user);

        // Empêcher la suppression de soi-même (double sécurité)
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;

        // Log de sécurité avant suppression
        \Log::warning('Utilisateur supprimé par un administrateur', [
            'deleted_by' => auth()->id(),
            'deleted_user_id' => $user->id,
            'deleted_user_email' => $user->email,
            'deleted_user_role' => $user->role,
        ]);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$userName} a été supprimé avec succès.");
    }
}



