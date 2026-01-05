# 🎨 EXEMPLES DE CODE - DESIGN SYSTEM WEGA
## Implémentation pratique des composants

---

## 📦 COMPOSANTS BLADE RÉUTILISABLES

### 1. Composant Button

**Fichier : `resources/views/components/button.blade.php`**

```blade
@props([
    'variant' => 'primary',    // primary, secondary, danger, success, outline-primary, outline-secondary
    'size' => 'md',            // sm, md, lg
    'type' => 'button',
    'icon' => null,
    'loading' => false,
    'disabled' => false,
    'href' => null,
])

@php
$baseClasses = 'btn';
$variantClasses = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'danger' => 'btn-danger',
    'success' => 'btn-success',
    'outline-primary' => 'btn-outline-primary',
    'outline-secondary' => 'btn-outline-secondary',
];
$sizeClasses = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg',
];

$classes = [
    $baseClasses,
    $variantClasses[$variant] ?? $variantClasses['primary'],
    $sizeClasses[$size] ?? '',
    $loading ? 'loading' : '',
    $disabled ? 'disabled' : '',
];

$classString = implode(' ', array_filter($classes));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classString]) }}
       @if($disabled || $loading) onclick="return false;" @endif>
        @if($icon && !$loading)
            <i class="{{ $icon }}"></i>
        @endif
        @if($loading)
            <span class="spinner"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classString]) }}
            @if($disabled || $loading) disabled @endif>
        @if($icon && !$loading)
            <i class="{{ $icon }}"></i>
        @endif
        @if($loading)
            <span class="spinner"></span>
        @endif
        {{ $slot }}
    </button>
@endif
```

**Usage :**

```blade
{{-- Bouton primaire simple --}}
<x-button variant="primary">Créer</x-button>

{{-- Bouton avec icône --}}
<x-button variant="primary" icon="bi bi-plus-circle">Nouveau Produit</x-button>

{{-- Bouton danger --}}
<x-button variant="danger" icon="bi bi-trash">Supprimer</x-button>

{{-- Bouton loading --}}
<x-button variant="primary" :loading="true">Enregistrement...</x-button>

{{-- Bouton comme lien --}}
<x-button variant="outline-secondary" href="{{ route('products.index') }}">
    Retour
</x-button>

{{-- Bouton désactivé --}}
<x-button variant="primary" :disabled="true">Action non disponible</x-button>
```

---

### 2. Composant Input

**Fichier : `resources/views/components/input.blade.php`**

```blade
@props([
    'type' => 'text',
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-control @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @elseif($help)
        <small class="text-muted">{{ $help }}</small>
    @enderror
</div>
```

**Usage :**

```blade
<x-input 
    name="libelle" 
    label="Libellé du produit" 
    placeholder="Entrez le libellé"
    required 
/>

<x-input 
    type="email" 
    name="email" 
    label="Email"
    help="Nous ne partagerons jamais votre email"
/>
```

---

### 3. Composant Select

**Fichier : `resources/views/components/select.blade.php`**

```blade
@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Sélectionnez une option',
    'required' => false,
    'disabled' => false,
    'help' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-select @error($name) is-invalid @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" 
                    @selected(old($name, $value) == $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @elseif($help)
        <small class="text-muted">{{ $help }}</small>
    @enderror
</div>
```

**Usage :**

```blade
<x-select 
    name="type_produit"
    label="Type de produit"
    :options="[
        'Gros' => 'En Gros',
        'Détail' => 'En Détail',
        'Les deux' => 'Les deux (Gros & Détail)'
    ]"
    required
/>

<x-select 
    name="categorie"
    label="Catégorie"
    :options="[
        'Vin & Boissons' => 'Vin & Boissons',
        'Produits Alimentaires' => 'Produits Alimentaires',
        'Produits de vitrine' => 'Produits de vitrine'
    ]"
    value="{{ old('categorie', $product->categorie ?? null) }}"
/>
```

---

### 4. Composant Card

**Fichier : `resources/views/components/card.blade.php`**

```blade
@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'class' => '',
])

<div class="card {{ $class }}">
    @if($title || $subtitle)
        <div class="card-header">
            @if($title)
                <h5 class="card-title mb-0">{{ $title }}</h5>
            @endif
            @if($subtitle)
                <p class="card-subtitle mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
```

**Usage :**

```blade
<x-card title="Informations Générales" subtitle="Détails du produit">
    <p>Contenu de la carte</p>
</x-card>

<x-card>
    <h5>Produits</h5>
    <p>Liste des produits</p>
    
    <x-slot name="footer">
        <x-button variant="primary">Ajouter</x-button>
    </x-slot>
</x-card>
```

---

### 5. Composant Alert

**Fichier : `resources/views/components/alert.blade.php`**

```blade
@props([
    'type' => 'info',  // success, danger, warning, info
    'dismissible' => false,
    'icon' => true,
])

@php
$iconMap = [
    'success' => 'bi-check-circle-fill',
    'danger' => 'bi-exclamation-circle-fill',
    'warning' => 'bi-exclamation-triangle-fill',
    'info' => 'bi-info-circle-fill',
];
$iconClass = $iconMap[$type] ?? 'bi-info-circle-fill';
@endphp

<div class="alert alert-{{ $type }}" role="alert">
    @if($icon)
        <i class="{{ $iconClass }} alert-icon"></i>
    @endif
    
    <div class="flex-grow-1">
        {{ $slot }}
    </div>
    
    @if($dismissible)
        <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Fermer">
            <i class="bi-x"></i>
        </button>
    @endif
</div>
```

**Usage :**

```blade
<x-alert type="success" dismissible>
    Produit créé avec succès !
</x-alert>

<x-alert type="danger">
    <strong>Erreur :</strong> Impossible de supprimer ce produit.
</x-alert>

<x-alert type="warning" icon>
    Veuillez vérifier les informations saisies.
</x-alert>
```

---

## 📋 EXEMPLES DE FORMULAIRES COMPLETS

### Formulaire de Création de Produit

```blade
<form action="{{ route('products.store') }}" method="POST" id="productForm">
    @csrf

    <x-card title="Informations Générales">
        <div class="row g-3">
            <div class="col-md-6">
                <x-select 
                    name="categorie"
                    label="Catégorie"
                    :options="[
                        'Vin & Boissons' => 'Vin & Boissons',
                        'Produits Alimentaires' => 'Produits Alimentaires',
                        'Produits de vitrine' => 'Produits de vitrine'
                    ]"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-input 
                    name="libelle" 
                    label="Libellé" 
                    placeholder="Nom du produit"
                    required 
                />
            </div>

            <div class="col-md-6">
                <x-select 
                    name="type_produit"
                    label="Type de produit"
                    :options="[
                        'Gros' => 'En Gros',
                        'Détail' => 'En Détail',
                        'Les deux' => 'Les deux (Gros & Détail)'
                    ]"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-select 
                    name="lieu"
                    label="Lieu"
                    :options="[
                        'Stock' => 'Stock',
                        'Boutique' => 'Boutique'
                    ]"
                    required
                />
            </div>
        </div>
    </x-card>

    <div class="d-flex justify-content-end gap-3 mt-4">
        <x-button variant="outline-secondary" href="{{ route('products.index') }}">
            <i class="bi bi-arrow-left"></i> Annuler
        </x-button>
        <x-button type="submit" variant="primary" icon="bi bi-check-lg">
            Créer le produit
        </x-button>
    </div>
</form>
```

---

## 📊 EXEMPLES DE TABLEAUX RESPONSIVE

### Tableau Desktop + Cards Mobile

```blade
{{-- Version Desktop --}}
<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Stock</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td><span class="badge badge-primary">{{ $product->code_produit }}</span></td>
                        <td>{{ $product->libelle }}</td>
                        <td>{{ $product->categorie }}</td>
                        <td>{{ number_format($product->stock_actuel ?? 0, 0, ',', ' ') }}</td>
                        <td class="text-end">
                            <x-button variant="outline-primary" size="sm" href="{{ route('products.show', $product) }}">
                                <i class="bi bi-eye"></i>
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Version Mobile --}}
<div class="d-md-none">
    @foreach($products as $product)
        <x-card class="mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1">{{ $product->libelle }}</h6>
                    <span class="badge badge-primary">{{ $product->code_produit }}</span>
                </div>
                <x-button variant="outline-primary" size="sm" href="{{ route('products.show', $product) }}">
                    <i class="bi bi-eye"></i>
                </x-button>
            </div>
            <p class="text-muted text-sm mb-2">{{ $product->categorie }}</p>
            <p class="mb-0"><strong>Stock :</strong> {{ number_format($product->stock_actuel ?? 0, 0, ',', ' ') }}</p>
        </x-card>
    @endforeach
</div>
```

---

## 🎯 EXEMPLE DE PAGE COMPLÈTE

### Page Index avec Recherche et Filtres

```blade
@extends('layouts.app')

@section('title', 'Produits - WEGA')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title">Gestion des Produits</h1>
            <p class="page-subtitle">Consultez et gérez votre catalogue de produits</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <x-button variant="outline-secondary" href="{{ route('inventories.index') }}">
                <i class="bi bi-clipboard-check"></i> Inventaires
            </x-button>
            <x-button variant="success" href="{{ route('supplies.create') }}">
                <i class="bi bi-cart-plus"></i> Approvisionnement
            </x-button>
            <x-button variant="primary" href="{{ route('products.create') }}">
                <i class="bi bi-plus-circle"></i> Nouveau Produit
            </x-button>
        </div>
    </div>
</div>

<x-card class="mb-4">
    <form method="GET" action="{{ route('products.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-10">
                <x-input 
                    name="search" 
                    label="Recherche"
                    placeholder="Rechercher par libellé, code produit ou catégorie..."
                    value="{{ request('search') }}"
                />
            </div>
            <div class="col-md-2">
                <x-button type="submit" variant="primary" class="w-100">
                    <i class="bi bi-search"></i> Rechercher
                </x-button>
            </div>
        </div>
    </form>
</x-card>

@if($products->count() > 0)
    <x-card>
        <div class="card-body p-0">
            {{-- Tableau Desktop --}}
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Libellé</th>
                                <th>Catégorie</th>
                                <th>Type</th>
                                <th>Stock Gros</th>
                                <th>Stock Détail</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td><span class="badge badge-primary">{{ $product->code_produit }}</span></td>
                                    <td>{{ $product->libelle }}</td>
                                    <td>{{ $product->categorie }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $product->type_produit }}</span>
                                    </td>
                                    <td>{{ number_format($product->stock_gros ?? 0, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($product->stock_detail ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <x-button variant="outline-primary" size="sm" href="{{ route('products.show', $product) }}">
                                                <i class="bi bi-eye"></i>
                                            </x-button>
                                            <x-button variant="outline-secondary" size="sm" href="{{ route('products.edit', $product) }}">
                                                <i class="bi bi-pencil"></i>
                                            </x-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cards Mobile --}}
            <div class="d-md-none p-4">
                @foreach($products as $product)
                    <x-card class="mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">{{ $product->libelle }}</h6>
                                <span class="badge badge-primary">{{ $product->code_produit }}</span>
                            </div>
                            <div class="d-flex gap-1">
                                <x-button variant="outline-primary" size="sm" href="{{ route('products.show', $product) }}">
                                    <i class="bi bi-eye"></i>
                                </x-button>
                                <x-button variant="outline-secondary" size="sm" href="{{ route('products.edit', $product) }}">
                                    <i class="bi bi-pencil"></i>
                                </x-button>
                            </div>
                        </div>
                        <p class="text-muted text-sm mb-2">{{ $product->categorie }} · {{ $product->type_produit }}</p>
                        <div class="d-flex gap-4">
                            <div>
                                <span class="text-muted text-sm">Stock Gros</span>
                                <p class="mb-0"><strong>{{ number_format($product->stock_gros ?? 0, 0, ',', ' ') }}</strong></p>
                            </div>
                            <div>
                                <span class="text-muted text-sm">Stock Détail</span>
                                <p class="mb-0"><strong>{{ number_format($product->stock_detail ?? 0, 0, ',', ' ') }}</strong></p>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    </x-card>
@else
    <x-card>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-muted);"></i>
            <p class="text-muted mt-3">Aucun produit trouvé</p>
            <x-button variant="primary" href="{{ route('products.create') }}">
                <i class="bi bi-plus-circle"></i> Créer le premier produit
            </x-button>
        </div>
    </x-card>
@endif
@endsection
```

---

## 🎨 UTILISATION DES CLASSES UTILITAIRES

```blade
{{-- Espacements --}}
<div class="mb-4 mt-2">  <!-- margin-bottom: 1rem, margin-top: 0.5rem -->
<div class="gap-3">      <!-- gap: 0.75rem -->

{{-- Text --}}
<p class="text-primary font-semibold">Texte important</p>
<p class="text-muted text-sm">Texte secondaire</p>

{{-- Display --}}
<div class="d-flex gap-2">
<div class="d-md-none">  <!-- Masqué sur desktop -->
<div class="d-none d-md-block">  <!-- Masqué sur mobile -->
```

---

**Ces exemples sont prêts à être utilisés dans votre application !** 🚀



