@php
    $title = 'Définir un nouveau mot de passe';
    $subtitle = 'Choisissez un mot de passe robuste pour sécuriser votre compte';
    $badge = 'Sécurité renforcée';
    $icon = 'bi bi-lock-fill';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required class="form-control">
        </div>
        <div>
            <label for="password">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required class="form-control">
        </div>
        <div>
            <label for="password_confirmation">Confirmez le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn-primary">Mettre à jour mon accès</button>
        <div class="form-footer">
            <a href="{{ route('login') }}">Retour à la connexion</a>
        </div>
    </form>
@endsection



