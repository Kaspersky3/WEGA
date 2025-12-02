@extends('layouts.app')

@section('title', 'Accueil - WEGA')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        border-radius: 1rem;
        margin-bottom: 3rem;
    }
    .feature-card {
        background: white;
        border-radius: 0.75rem;
        padding: 2rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    .feature-icon {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }
</style>
@endpush

@section('content')
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Bienvenue sur WEGA</h1>
        <p class="lead mb-4">Système de gestion d'inventaire et de stock professionnel</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            @auth
                <a href="{{ route('inventories.index') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-clipboard-check"></i> Accéder aux Inventaires
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Se Connecter
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-person-plus"></i> Créer un Compte
                </a>
            @endauth
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <h4 class="fw-bold mb-3">Gestion d'Inventaire</h4>
            <p class="text-muted">Créez et gérez vos inventaires physiques avec précision. Suivez les écarts entre stocks théoriques et réels.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="bi bi-box"></i>
            </div>
            <h4 class="fw-bold mb-3">Catalogue Produits</h4>
            <p class="text-muted">Gérez votre catalogue de produits avec support des ventes en gros et en détail. Suivi des stocks en temps réel.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="bi bi-graph-up"></i>
            </div>
            <h4 class="fw-bold mb-3">Analytics Avancés</h4>
            <p class="text-muted">Analysez les performances de vos produits. Identifiez les meilleures ventes et les produits les plus rentables.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fonctionnalités Principales</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Inventaires physiques</strong> avec calcul automatique des écarts
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Gestion des produits</strong> avec support gros/détail
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Approvisionnements</strong> avec mise à jour automatique des stocks
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Export/Import CSV</strong> pour faciliter la gestion
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Analytics</strong> avec graphiques et rapports détaillés
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Accès Rapide</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Créer un Inventaire
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box"></i> Voir les Produits
                    </a>
                    <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-primary">
                        <i class="bi bi-graph-up"></i> Consulter les Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
