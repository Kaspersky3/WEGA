# Dockerisation du projet WEGA

Ce projet a été dockerisé pour faciliter le développement et le déploiement.

## Prérequis

- Docker
- Docker Compose

## Configuration

### Option 1 : Script d'initialisation automatique

**Windows PowerShell :**
```powershell
.\docker-init.ps1
```

**Linux/Mac :**
```bash
chmod +x docker/init.sh
./docker/init.sh
```

### Option 2 : Configuration manuelle

1. Copiez le fichier `env.docker.example` vers `.env` :
   ```bash
   # Windows PowerShell
   Copy-Item env.docker.example .env
   
   # Linux/Mac
   cp env.docker.example .env
   ```

2. Démarrer les conteneurs :
   ```bash
   docker-compose up -d
   ```

3. Générez la clé d'application Laravel :
   ```bash
   docker-compose exec app php artisan key:generate
   ```

4. Configurez les variables d'environnement dans `.env` selon vos besoins.
   Voir `docker/ENV_VARIABLES.md` pour plus de détails.

## Utilisation

### Démarrer les conteneurs

```bash
docker-compose up -d
```

### Arrêter les conteneurs

```bash
docker-compose down
```

### Voir les logs

```bash
docker-compose logs -f
```

### Exécuter des commandes Artisan

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### Installer les dépendances Composer

```bash
docker-compose exec app composer install
```

### Installer les dépendances NPM

```bash
docker-compose exec node npm install
```

### Compiler les assets (Vite)

```bash
docker-compose exec node npm run build
```

## Accès à l'application

- **Application web** : http://localhost:8000
- **Vite Dev Server** : http://localhost:5173 (développement)
- **MySQL** : localhost:3306
- **Redis** : localhost:6379

## Variables d'environnement importantes

⚠️ **Important** : Dans votre fichier `.env`, assurez-vous que :
- `DB_HOST=db` (pas `localhost` ou `127.0.0.1`)
- `REDIS_HOST=redis` (pas `localhost` ou `127.0.0.1`)

Ces noms correspondent aux services définis dans `docker-compose.yml`.

## Services

- **app** : Application PHP-FPM (Laravel)
- **nginx** : Serveur web Nginx
- **db** : Base de données MySQL 8.0
- **redis** : Cache Redis
- **node** : Service Node.js pour Vite (développement)

## Commandes utiles supplémentaires

### Accéder au shell du conteneur PHP
```bash
docker-compose exec app bash
```

### Voir les logs d'un service spécifique
```bash
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Redémarrer un service
```bash
docker-compose restart app
```

### Reconstruire les images
```bash
docker-compose build
docker-compose up -d
```

### Supprimer tous les conteneurs et volumes (⚠️ supprime les données)
```bash
docker-compose down -v
```

## Notes

- Les données de la base de données sont persistées dans un volume Docker nommé `db_data`
- Les fichiers de l'application sont montés en volume pour le développement
- Pour la production, modifiez le Dockerfile pour ne pas monter les volumes et optimiser les builds
- Le service Node.js est optionnel et peut être désactivé si vous compilez les assets localement

