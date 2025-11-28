# Guide de déploiement sur Render

Ce guide vous explique comment déployer votre application Laravel WEGA sur Render.

## Prérequis

1. Un compte Render (gratuit) : https://render.com
2. Votre code sur GitHub, GitLab ou Bitbucket
3. Docker installé localement (pour tester)

## Option 1 : Déploiement avec render.yaml (Recommandé)

### Étape 1 : Préparer votre dépôt Git

1. Assurez-vous que tous les fichiers sont commités :
   ```bash
   git add .
   git commit -m "Préparation pour le déploiement Render"
   git push
   ```

### Étape 2 : Créer les services sur Render

1. Connectez-vous à votre compte Render : https://dashboard.render.com
2. Cliquez sur **"New +"** puis **"Blueprint"**
3. Connectez votre dépôt Git
4. Render détectera automatiquement le fichier `render.yaml` et créera tous les services

### Étape 3 : Configurer les variables d'environnement

Après la création des services, vous devez configurer les variables d'environnement dans le dashboard Render :

1. Allez dans votre service web (wega-app)
2. Cliquez sur **"Environment"**
3. Ajoutez les variables suivantes :

```env
APP_NAME=WEGA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.onrender.com
APP_KEY=base64:... (généré automatiquement ou manuellement)

LOG_CHANNEL=stack
LOG_LEVEL=error

# La base de données sera configurée automatiquement via render.yaml
# Mais vous pouvez aussi définir manuellement :
DB_CONNECTION=mysql
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD seront injectés automatiquement

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
QUEUE_CONNECTION=redis

# Redis sera configuré automatiquement via render.yaml
# REDIS_URL sera injecté automatiquement
```

### Étape 4 : Générer la clé d'application

1. Dans le service web, allez dans **"Shell"**
2. Exécutez :
   ```bash
   php artisan key:generate
   ```
3. Copiez la clé générée et ajoutez-la dans les variables d'environnement comme `APP_KEY`

### Étape 5 : Exécuter les migrations

1. Dans le **"Shell"** du service web, exécutez :
   ```bash
   php artisan migrate --force
   ```

### Étape 6 : Optionnel - Exécuter les seeders

```bash
php artisan db:seed
```

## Option 2 : Déploiement manuel

### Étape 1 : Créer la base de données

1. Dans Render Dashboard, cliquez sur **"New +"** → **"PostgreSQL"** ou **"MySQL"**
2. Configurez :
   - **Name** : wega-db
   - **Database** : wega
   - **User** : wega
   - **Region** : Choisissez la région la plus proche
   - **Plan** : Free (pour commencer)
3. Notez les informations de connexion

### Étape 2 : Créer Redis (optionnel mais recommandé)

1. Cliquez sur **"New +"** → **"Redis"**
2. Configurez :
   - **Name** : wega-redis
   - **Region** : Même région que la base de données
   - **Plan** : Free (pour commencer)

### Étape 3 : Créer le service web

1. Cliquez sur **"New +"** → **"Web Service"**
2. Connectez votre dépôt Git
3. Configurez :
   - **Name** : wega-app
   - **Region** : Même région que la base de données
   - **Branch** : main (ou votre branche de production)
   - **Root Directory** : (laissez vide)
   - **Runtime** : Docker
   - **Dockerfile Path** : `Dockerfile.prod`
   - **Docker Context** : `.`
   - **Build Command** : (laissez vide, géré par Dockerfile)
   - **Start Command** : (laissez vide, géré par Dockerfile)
   - **Plan** : Free (pour commencer)

### Étape 4 : Configurer les variables d'environnement

Dans les paramètres du service web, ajoutez toutes les variables d'environnement listées dans l'Option 1, Étape 3.

### Étape 5 : Connecter la base de données

1. Dans les variables d'environnement, ajoutez :
   - `DB_HOST` : L'hôte de votre base de données Render
   - `DB_PORT` : Le port (généralement 3306 pour MySQL, 5432 pour PostgreSQL)
   - `DB_DATABASE` : Le nom de la base de données
   - `DB_USERNAME` : Le nom d'utilisateur
   - `DB_PASSWORD` : Le mot de passe

   **Ou** utilisez `DATABASE_URL` fourni par Render.

### Étape 6 : Générer la clé et exécuter les migrations

Comme dans l'Option 1, étapes 4-6.

## Configuration des variables d'environnement importantes

### Variables requises

```env
APP_NAME=WEGA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.onrender.com
APP_KEY=base64:... (généré avec php artisan key:generate)
```

### Variables de base de données

Si vous utilisez `render.yaml`, `DATABASE_URL` est injecté automatiquement. Laravel peut utiliser cette URL directement.

Sinon, configurez manuellement :

```env
DB_CONNECTION=mysql  # ou pgsql pour PostgreSQL
DB_HOST=...          # Fourni par Render (ex: dpg-xxxxx-a.frankfurt-postgres.render.com)
DB_PORT=3306         # ou 5432 pour PostgreSQL
DB_DATABASE=wega
DB_USERNAME=wega
DB_PASSWORD=...      # Fourni par Render
```

**Note** : Si vous utilisez `DATABASE_URL`, Laravel l'utilisera automatiquement et ignorera les variables individuelles.

### Variables Redis

Si vous utilisez `render.yaml`, `REDIS_URL` est injecté automatiquement. Sinon :

```env
REDIS_URL=redis://...  # Fourni par Render
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## Commandes utiles via Shell Render

Accédez au Shell de votre service web dans le dashboard Render :

```bash
# Voir les logs
tail -f storage/logs/laravel.log

# Exécuter les migrations
php artisan migrate --force

# Exécuter les seeders
php artisan db:seed

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Dépannage

### L'application ne démarre pas

1. Vérifiez les logs dans le dashboard Render
2. Vérifiez que toutes les variables d'environnement sont définies
3. Vérifiez que `APP_KEY` est générée
4. Vérifiez que la base de données est accessible

### Erreurs de connexion à la base de données

1. Vérifiez que les variables `DB_*` sont correctement configurées
2. Vérifiez que la base de données est dans la même région
3. Vérifiez que le service web peut accéder à la base de données (pas de restrictions réseau)

### Erreurs 500

1. Activez temporairement `APP_DEBUG=true` pour voir les erreurs
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Vérifiez les permissions des dossiers `storage` et `bootstrap/cache`

### Les assets ne se chargent pas

1. Vérifiez que `npm run build` s'est exécuté correctement lors du build
2. Vérifiez que le dossier `public/build` existe
3. Vérifiez la configuration de `vite.config.js`

## Optimisations pour la production

1. **Cache Laravel** : Les caches sont automatiquement créés dans le Dockerfile
2. **OPcache** : Activé dans `docker/php/production.ini`
3. **Gzip** : Activé dans la configuration Nginx
4. **Headers de sécurité** : Configurés dans Nginx

## Coûts

- **Free Plan** : Gratuit mais avec limitations (spins down après inactivité)
- **Starter Plan** : ~$7/mois - Toujours actif
- **Standard Plan** : ~$25/mois - Plus de ressources

## Support

- Documentation Render : https://render.com/docs
- Support Render : support@render.com
- Dashboard : https://dashboard.render.com

