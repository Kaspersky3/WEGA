#!/bin/bash
set -e

echo "🚀 Build de l'application pour Render..."

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY n'est pas définie. Elle sera générée au démarrage."
fi

# Compiler les assets
echo "📦 Compilation des assets..."
npm ci
npm run build

# Installer les dépendances Composer
echo "📦 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader --no-interaction

# Optimiser Laravel
echo "⚡ Optimisation de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build terminé!"

