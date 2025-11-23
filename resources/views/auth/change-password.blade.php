@php
    $title = 'Changer mon mot de passe';
    $subtitle = 'Prenez une longueur d’avance sur la sécurité';
    $badge = 'Mon compte';
    $icon = 'bi bi-shield-lock-fill';
@endphp

@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('password.change.update') }}" style="display:flex; flex-direction:column; gap:1.25rem;">
        @csrf
        @method('PUT')
        <div>
            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" type="password" name="current_password" required class="form-control">
        </div>
        <div>
            <label for="password">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required class="form-control">
        </div>
        <div>
            <label for="password_confirmation">Confirmez le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn-primary">Mettre à jour</button>
        <div class="form-footer">
            <a href="{{ url()->previous() }}">Retour</a>
        </div>
    </form>
@endsection



