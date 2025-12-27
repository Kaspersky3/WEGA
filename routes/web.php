<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ChangePasswordController;
use Illuminate\Support\Facades\Route;

// Page d'accueil : redirection vers login si non connecté, sinon vers inventaires
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('inventories.index');
    }
    return redirect()->route('login');
})->name('home');

// Auth - invités
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Auth - protégées
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/settings/password', [ChangePasswordController::class, 'edit'])->name('password.change.edit');
    Route::put('/settings/password', [ChangePasswordController::class, 'update'])->name('password.change.update');
});

// Routes protégées - nécessitent une authentification
Route::middleware('auth')->group(function () {
    // Routes produits
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Routes approvisionnements
    Route::get('/supplies/create', [SupplyController::class, 'create'])->name('supplies.create');
    Route::post('/supplies', [SupplyController::class, 'store'])->name('supplies.store');
    Route::get('/api/products/search', [SupplyController::class, 'searchProducts'])->name('api.products.search');

    // Routes inventaires
    Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
    Route::get('/inventories/create', [InventoryController::class, 'create'])->name('inventories.create');
    Route::post('/inventories', [InventoryController::class, 'store'])->name('inventories.store');
    Route::post('/inventories/import', [InventoryController::class, 'import'])->name('inventories.import');
    Route::get('/inventories/history/export', [InventoryController::class, 'historyExport'])->name('inventories.history.export');
    Route::get('/inventories/{inventory}/export/{format}', [InventoryController::class, 'detailsExport'])
        ->whereIn('format', ['csv', 'xlsx'])
        ->name('inventories.details.export');
    Route::get('/inventories/{inventory}', [InventoryController::class, 'show'])->name('inventories.show');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');
    Route::get('/analytics/export/{type}/{format}', [AnalyticsController::class, 'export'])
        ->whereIn('type', ['top-selling', 'top-profitable'])
        ->whereIn('format', ['csv', 'xlsx'])
        ->name('analytics.export');
});

// Routes admin - Gestion des utilisateurs (seulement pour les admins)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
});
