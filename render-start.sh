#!/bin/sh
set -e

echo "🚀 Démarrage de l'application..."

# Attendre que la base de données soit prête (si nécessaire)
# Cette partie peut être adaptée selon vos besoins

# Générer la clé d'application si elle n'existe pas
if [ -z "$APP_KEY" ]; then
    echo "⚠️  Génération de APP_KEY..."
    php artisan key:generate --force || true
fi

# Exécuter les migrations (optionnel, peut être fait manuellement)
# php artisan migrate --force || true

# Optimiser Laravel
echo "⚡ Optimisation de Laravel..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Application prête!"

# Démarrer Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

