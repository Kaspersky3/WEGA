@php
    $title = 'Connexion';
    $subtitle = 'Accédez à votre cockpit analytique';
    $badge = 'Sécurité avancée';
    $icon = 'bi bi-door-open';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:1.5rem;">
        @csrf
        <div>
            <label for="user_type">Type d'utilisateur <span style="color:var(--danger);">*</span></label>
            <select id="user_type" name="user_type" required class="form-control" style="cursor:pointer;">
                <option value="">Sélectionnez un type</option>
                <option value="admin" {{ old('user_type') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                <option value="user" {{ old('user_type') === 'user' ? 'selected' : '' }}>Utilisateur</option>
            </select>
            @error('user_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror" placeholder="vous@wega.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-check">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">Se souvenir de moi</label>
        </div>
        <button type="submit" class="btn-primary">
            Se connecter
        </button>
        <div class="form-footer" style="margin-top:1rem; padding-top:1.5rem; border-top:1px solid var(--border);">
            <small>
                <i class="bi bi-info-circle"></i> Seuls les administrateurs peuvent créer des comptes utilisateurs.
            </small>
        </div>
    </form>
@endsection





