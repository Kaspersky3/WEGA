@php
    $title = 'Créer un compte';
    $subtitle = 'Rejoignez la plateforme en quelques secondes';
    $badge = 'Bienvenue';
    $icon = 'bi bi-stars';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('register') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        <div>
            <label for="name">Nom complet</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="form-control" placeholder="Awa Diarra">
        </div>
        <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="awa@wega.com">
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required class="form-control" placeholder="••••••••">
        </div>
        <div>
            <label for="password_confirmation">Confirmez le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-primary">Créer mon espace</button>
        <div class="form-footer">
            Déjà membre ?
            <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </form>
@endsection



