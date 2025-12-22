<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Tentative de connexion
        if (! Auth::attempt($request->credentials(), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Identifiants incorrects.',
            ])->withInput($request->only('email', 'user_type'));
        }

        // Vérifier que le type d'utilisateur correspond
        $user = Auth::user();
        $requestedType = strtolower($request->input('user_type'));
        $userRole = strtolower($user->role ?? 'user');
        
        // Log pour déboguer (à supprimer en production si nécessaire)
        \Log::info('Tentative de connexion', [
            'email' => $user->email,
            'requested_type' => $requestedType,
            'user_role' => $userRole,
            'match' => $userRole === $requestedType,
        ]);
        
        if ($userRole !== $requestedType) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'user_type' => 'Le type d\'utilisateur sélectionné ne correspond pas à votre compte. Votre compte est de type : ' . ucfirst($userRole) . '.',
            ])->withInput($request->only('email', 'user_type'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('inventories.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Vous êtes déconnecté.');
    }
}





