# 🔐 Guide de Déploiement Sécurisé - Sevalla

## 📋 Checklist de Sécurité Laravel en Production

Ce document décrit les étapes complètes pour déployer l'application WEGA de manière sécurisée sur Sevalla.

---

## ✅ 1. Configuration des Variables d'Environnement

### Variables CRITIQUES pour la sécurité

Copiez `env.production.example` vers `.env` et configurez :

```env
# ⚠️ OBLIGATOIRE - Environnement production
APP_ENV=production
APP_DEBUG=false

# ⚠️ OBLIGATOIRE - URL HTTPS complète (pas HTTP)
APP_URL=https://votre-domaine.sevalla.com

# ⚠️ OBLIGATOIRE - Clé d'application générée
APP_KEY=base64:VOTRE_CLE_GENEREE_ICI

# ⚠️ OBLIGATOIRE - Cookies sécurisés HTTPS
SESSION_SECURE_COOKIE=true

# Base de données
DB_CONNECTION=mysql
DB_HOST=votre-host-sevalla
DB_PORT=3306
DB_DATABASE=wega
DB_USERNAME=votre-user
DB_PASSWORD=votre-password-securise

# Sessions
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### ⚠️ Erreurs courantes à éviter

1. **APP_URL en HTTP** → Les formulaires seront bloqués
   ```env
   ❌ APP_URL=http://votre-domaine.sevalla.com
   ✅ APP_URL=https://votre-domaine.sevalla.com
   ```

2. **SESSION_SECURE_COOKIE=false** → Cookies non sécurisés, avertissements navigateur
   ```env
   ❌ SESSION_SECURE_COOKIE=false
   ✅ SESSION_SECURE_COOKIE=true
   ```

3. **APP_DEBUG=true** → Exposition des erreurs et informations sensibles
   ```env
   ❌ APP_DEBUG=true
   ✅ APP_DEBUG=false
   ```

---

## 🔧 2. Génération de la Clé d'Application

```bash
php artisan key:generate
```

Copiez la clé générée dans votre fichier `.env` :
```env
APP_KEY=base64:LA_CLE_GENEREE
```

---

## 🗄️ 3. Configuration de la Base de Données

### Exécuter les migrations

```bash
php artisan migrate --force
```

### Créer un utilisateur administrateur (si nécessaire)

```bash
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('MotDePasseSecurise123!'),
    'role' => 'admin'
]);
```

---

## 🔒 4. Vérification de la Sécurité

### ✅ Routes protégées

Toutes les routes CRUD sont maintenant protégées par le middleware `auth` :
- ✅ `/products/*` → Authentification requise
- ✅ `/supplies/*` → Authentification requise
- ✅ `/inventories/*` → Authentification requise
- ✅ `/analytics/*` → Authentification requise

### ✅ HTTPS forcé

Le middleware `ForceHttps` redirige automatiquement HTTP → HTTPS en production.

### ✅ Cookies sécurisés

Les cookies de session sont :
- ✅ Transmis uniquement en HTTPS (`SESSION_SECURE_COOKIE=true`)
- ✅ Accessibles uniquement via HTTP (`http_only=true`)
- ✅ Protégés contre CSRF (`same_site=lax`)

### ✅ Protection CSRF

Tous les formulaires incluent le token CSRF :
- ✅ `@csrf` dans tous les formulaires Blade
- ✅ Middleware `VerifyCsrfToken` actif

---

## 🌐 5. Configuration Serveur Sevalla

### Headers de Sécurité recommandés

Ajoutez ces headers dans votre configuration Nginx/Apache :

```nginx
# Forcer HTTPS
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

# Protection XSS
add_header X-XSS-Protection "1; mode=block" always;

# Empêcher le MIME sniffing
add_header X-Content-Type-Options "nosniff" always;

# Protection contre le clickjacking
add_header X-Frame-Options "SAMEORIGIN" always;

# Politique de référent
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

### Redirection HTTP → HTTPS

Si Sevalla ne le fait pas automatiquement, ajoutez dans votre configuration :

```nginx
server {
    listen 80;
    server_name votre-domaine.sevalla.com;
    return 301 https://$server_name$request_uri;
}
```

---

## 🧪 6. Tests de Validation

### Test 1 : Vérifier HTTPS

1. Accédez à `http://votre-domaine.sevalla.com`
2. ✅ Vous devez être redirigé vers `https://votre-domaine.sevalla.com`

### Test 2 : Vérifier l'authentification

1. Accédez à `https://votre-domaine.sevalla.com/products`
2. ✅ Vous devez être redirigé vers `/login`
3. Connectez-vous avec vos identifiants
4. ✅ Vous devez pouvoir accéder aux produits

### Test 3 : Vérifier les formulaires

1. Connectez-vous
2. Créez un nouveau produit
3. ✅ Le formulaire doit s'enregistrer sans avertissement
4. ✅ Aucun message "informations non sécurisées"

### Test 4 : Vérifier les cookies

1. Ouvrez les outils de développement (F12)
2. Onglet "Application" → "Cookies"
3. ✅ Les cookies doivent avoir l'attribut `Secure`
4. ✅ Les cookies doivent avoir l'attribut `HttpOnly`

---

## 🐛 7. Résolution des Problèmes Courants

### Problème : "Les informations que vous êtes sur le point de soumettre ne sont pas sécurisées"

**Cause** : `SESSION_SECURE_COOKIE=false` ou `APP_URL` en HTTP

**Solution** :
```env
SESSION_SECURE_COOKIE=true
APP_URL=https://votre-domaine.sevalla.com
```

Puis redémarrez l'application.

### Problème : Formulaire ne s'enregistre pas

**Causes possibles** :
1. Token CSRF manquant → Vérifier `@csrf` dans le formulaire
2. Route non protégée → Vérifier que la route est dans `middleware('auth')`
3. Erreur de validation → Vérifier les logs Laravel

**Solution** :
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les routes
php artisan route:list
```

### Problème : Redirection infinie HTTPS

**Cause** : `TrustProxies` mal configuré

**Solution** : Vérifier que `TrustProxies.php` contient :
```php
protected $proxies = '*';
```

### Problème : Accès non autorisé aux routes

**Cause** : Routes non protégées

**Solution** : Vérifier que toutes les routes CRUD sont dans :
```php
Route::middleware('auth')->group(function () {
    // Routes protégées ici
});
```

---

## 📊 8. Monitoring et Logs

### Vérifier les logs d'erreur

```bash
tail -f storage/logs/laravel.log
```

### Vérifier les tentatives d'accès non autorisé

Les tentatives d'accès aux routes protégées sans authentification sont automatiquement redirigées vers `/login` et enregistrées dans les logs.

---

## 🔄 9. Mise à Jour de l'Application

Après chaque mise à jour :

1. **Puller les changements**
   ```bash
   git pull origin main
   ```

2. **Installer les dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Exécuter les migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Optimiser l'application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Vider les caches si nécessaire**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

---

## ✅ 10. Checklist Finale

Avant de mettre en production, vérifier :

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://...` (pas HTTP)
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `APP_KEY` généré et configuré
- [ ] Toutes les routes CRUD protégées par `auth`
- [ ] HTTPS fonctionne (redirection automatique)
- [ ] Formulaire d'approvisionnement fonctionne
- [ ] Aucun avertissement navigateur
- [ ] Cookies sécurisés (attribut `Secure`)
- [ ] Base de données migrée
- [ ] Utilisateur admin créé
- [ ] Logs configurés (`LOG_LEVEL=error`)

---

## 📞 Support

En cas de problème :

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier la configuration : `php artisan config:show`
3. Vérifier les routes : `php artisan route:list`
4. Vérifier l'environnement : `php artisan env`

---

## 🔐 Sécurité Continue

### Bonnes pratiques

1. **Mots de passe forts** : Utilisez des mots de passe complexes pour tous les comptes
2. **Mises à jour régulières** : Maintenez Laravel et les dépendances à jour
3. **Backups réguliers** : Sauvegardez régulièrement la base de données
4. **Monitoring** : Surveillez les logs pour détecter les tentatives d'intrusion
5. **HTTPS uniquement** : Ne jamais utiliser HTTP en production

### Audit de sécurité

Effectuez régulièrement :
- Vérification des permissions de fichiers
- Audit des logs d'accès
- Test des fonctionnalités d'authentification
- Vérification des headers de sécurité

---

**Dernière mise à jour** : 2025-01-XX
**Version Laravel** : 10.x
**Environnement** : Production (Sevalla)

