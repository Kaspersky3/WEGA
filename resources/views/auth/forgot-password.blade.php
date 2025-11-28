@php
    $title = 'Réinitialiser le mot de passe';
    $subtitle = 'Recevez un lien sécurisé pour définir un nouveau mot de passe';
    $badge = 'Assistance';
    $icon = 'bi bi-envelope-paper';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="vous@wega.com">
        </div>
        <button type="submit" class="btn-primary">Envoyer le lien magique</button>
        <div class="form-footer">
            <a href="{{ route('login') }}">Retour à la connexion</a>
        </div>
    </form>
@endsection





