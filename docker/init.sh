#!/bin/bash

echo "Initialisation du projet WEGA avec Docker..."

# Attendre que MySQL soit prêt
echo "Attente de la base de données..."
until docker-compose exec -T db mysqladmin ping -h localhost --silent; do
  echo "En attente de MySQL..."
  sleep 2
done

echo "MySQL est prêt!"

# Installer les dépendances Composer si nécessaire
if [ ! -d "vendor" ]; then
    echo "Installation des dépendances Composer..."
    docker-compose exec -T app composer install
fi

# Installer les dépendances NPM si nécessaire
if [ ! -d "node_modules" ]; then
    echo "Installation des dépendances NPM..."
    docker-compose exec -T node npm install
fi

# Copier .env si nécessaire
if [ ! -f ".env" ]; then
    echo "Création du fichier .env..."
    if [ -f "env.docker.example" ]; then
        cp env.docker.example .env
    else
        cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env
    fi
fi

# Générer la clé d'application
echo "Génération de la clé d'application..."
docker-compose exec -T app php artisan key:generate --force

# Exécuter les migrations
echo "Exécution des migrations..."
docker-compose exec -T app php artisan migrate --force

echo "Initialisation terminée!"
echo "L'application est accessible sur http://localhost:8000"

