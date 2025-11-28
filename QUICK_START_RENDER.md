# Déploiement rapide sur Render

## 🚀 Déploiement en 5 minutes

### 1. Préparer votre code

```bash
# Assurez-vous que tout est commité
git add .
git commit -m "Prêt pour le déploiement Render"
git push
```

### 2. Créer un compte Render

1. Allez sur https://render.com
2. Créez un compte (gratuit)
3. Connectez votre dépôt GitHub/GitLab/Bitbucket

### 3. Déployer avec Blueprint (Recommandé)

1. Dans Render Dashboard, cliquez sur **"New +"** → **"Blueprint"**
2. Sélectionnez votre dépôt
3. Render détectera automatiquement `render.yaml`
4. Cliquez sur **"Apply"**
5. Tous les services seront créés automatiquement !

### 4. Configurer les variables d'environnement

Dans le service web (wega-app), ajoutez dans **Environment** :

```env
APP_NAME=WEGA
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.onrender.com
```

### 5. Générer la clé d'application

Dans le **Shell** du service web :

```bash
php artisan key:generate
```

Copiez la clé et ajoutez-la comme `APP_KEY` dans les variables d'environnement.

### 6. Exécuter les migrations

```bash
php artisan migrate --force
```

### 7. C'est prêt ! 🎉

Votre application est accessible sur l'URL fournie par Render.

## 📝 Notes importantes

- **Plan Free** : L'application se met en veille après 15 minutes d'inactivité
- **Plan Starter** (~$7/mois) : Toujours actif
- Les variables de base de données et Redis sont injectées automatiquement via `render.yaml`
- Pour PostgreSQL, modifiez `render.yaml` et changez `DB_CONNECTION=pgsql` dans les variables d'environnement

## 🔧 Commandes utiles

```bash
# Voir les logs
tail -f storage/logs/laravel.log

# Vider le cache
php artisan cache:clear
php artisan config:clear

# Optimiser
php artisan optimize
```

## 📚 Documentation complète

Voir `DEPLOY_RENDER.md` pour le guide complet.

