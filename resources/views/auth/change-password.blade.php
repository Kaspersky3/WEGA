@extends('layouts.app')

@section('title', 'Changer le Mot de Passe - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Changer mon Mot de Passe</h1>
            <p class="page-subtitle">Mettez à jour votre mot de passe pour sécuriser votre compte</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="current_password" class="form-label fw-semibold">Mot de passe actuel <span class="text-danger">*</span></label>
                        <input id="current_password" type="password" name="current_password" required class="form-control @error('current_password') is-invalid @enderror" placeholder="Entrez votre mot de passe actuel">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                        <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror" placeholder="Choisissez un nouveau mot de passe">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirmez le mot de passe <span class="text-danger">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control" placeholder="Confirmez votre nouveau mot de passe">
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
