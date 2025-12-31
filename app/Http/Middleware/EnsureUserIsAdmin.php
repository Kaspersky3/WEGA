<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour s'assurer que l'utilisateur est administrateur
 * 
 * Ce middleware protège les routes administratives en vérifiant
 * que l'utilisateur authentifié a le rôle 'admin'.
 * 
 * Si l'utilisateur n'est pas admin, il est redirigé vers la page d'accueil
 * avec un message d'erreur.
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier que l'utilisateur est administrateur
        if (!auth()->user()->isAdmin()) {
            // Log de sécurité : tentative d'accès non autorisé
            \Log::warning('Tentative d\'accès non autorisé à une route admin', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'route' => $request->route()->getName(),
                'ip' => $request->ip(),
            ]);

            return redirect()->route('inventories.index')
                ->with('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}



