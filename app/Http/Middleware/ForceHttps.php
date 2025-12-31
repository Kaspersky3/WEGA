<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour forcer HTTPS en production
 * 
 * Ce middleware redirige toutes les requêtes HTTP vers HTTPS
 * lorsque l'application est en mode production.
 * 
 * Nécessaire pour :
 * - Sécuriser les cookies de session
 * - Éviter les avertissements de sécurité du navigateur
 * - Conformité avec les standards de sécurité web
 */
class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // En production, forcer HTTPS
        if (app()->environment('production') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}



