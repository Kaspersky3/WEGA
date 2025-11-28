@php
    $title = 'Connexion';
    $subtitle = 'Accédez à votre cockpit analytique';
    $badge = 'Sécurité avancée';
    $icon = 'bi bi-door-open';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control" placeholder="vous@wega.com">
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required class="form-control" placeholder="••••••••">
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; color:#cbd5f5;">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color:#7551FF;">
                Se souvenir de moi
            </label>
            <a href="{{ route('password.request') }}" style="color:#fff; font-weight:600;">Mot de passe oublié ?</a>
        </div>
        <button type="submit" class="btn-primary">
            Se connecter
        </button>
        <div class="form-footer">
            Nouveau sur WEGA ?
            <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    </form>
@endsection





