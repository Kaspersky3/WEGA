@extends('layouts.app')

@section('title', 'Détails - ' . $user->name . ' - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Détails de l'Utilisateur</h1>
            <p class="page-subtitle">{{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-semibold mb-3">Informations personnelles</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 150px;">Nom :</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Email :</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Rôle :</td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-danger">Administrateur</span>
                                    @else
                                        <span class="badge bg-secondary">Utilisateur</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-semibold mb-3">Informations système</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-semibold" style="width: 150px;">Créé le :</td>
                                <td>{{ $user->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Modifié le :</td>
                                <td>{{ $user->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            @if($user->email_verified_at)
                                <tr>
                                    <td class="fw-semibold">Email vérifié :</td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Oui
                                        </span>
                                        ({{ $user->email_verified_at->format('d/m/Y') }})
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="fw-semibold">Email vérifié :</td>
                                    <td>
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-circle"></i> Non
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($user->id === auth()->id())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Ceci est votre propre profil.
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Retour à la liste</a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



