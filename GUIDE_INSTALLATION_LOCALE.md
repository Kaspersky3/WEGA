# Guide d'Installation Locale - WEGA

## 📋 Prérequis à installer

### 1. PHP 8.1 ou supérieur

**Option A : Installation avec XAMPP (Recommandé pour débutants)**

1. Téléchargez XAMPP : https://www.apachefriends.org/download.html
2. Installez XAMPP (inclut PHP, MySQL, Apache)
3. Ajoutez PHP au PATH :
   - Ouvrez les Variables d'environnement Windows
   - Ajoutez `C:\xampp\php` au PATH système
   - Redémarrez PowerShell

**Option B : Installation PHP standalone**

1. Téléchargez PHP : https://windows.php.net/download/
2. Extrayez dans `C:\php`
3. Ajoutez `C:\php` au PATH système
4. Copiez `php.ini-development` vers `php.ini`
5. Décommentez les extensions nécessaires dans `php.ini` :
   ```
   extension=mysqli
   extension=pdo_mysql
   extension=mbstring
   extension=openssl
   extension=curl
   extension=fileinfo
   extension=zip
   extension=gd
   ```

### 2. Composer

1. Téléchargez Composer : https://getcomposer.org/download/
2. Exécutez l'installateur Windows
3. Vérifiez l'installation : `composer --version`

### 3. MySQL 8.0

**Option A : Avec XAMPP (si vous avez choisi XAMPP)**
- MySQL est inclus dans XAMPP
- Démarrez MySQL depuis le panneau de contrôle XAMPP

**Option B : Installation MySQL standalone**

1. Téléchargez MySQL : https://dev.mysql.com/downloads/installer/
2. Choisissez "MySQL Installer for Windows"
3. Installez MySQL Server 8.0
4. Notez le mot de passe root que vous définissez
5. Ajoutez MySQL au PATH si nécessaire

### 4. Redis (Optionnel - peut être remplacé par 'file')

**Option A : Utiliser Redis (Recommandé pour production)**

1. Téléchargez Redis pour Windows : https://github.com/microsoftarchive/redis/releases
2. Ou utilisez WSL2 avec Redis

**Option B : Utiliser le driver 'file' (Plus simple pour développement)**

- Pas besoin d'installer Redis
- Modifiez `.env` pour utiliser `CACHE_DRIVER=file` et `SESSION_DRIVER=file`

## 🚀 Installation du Projet

Une fois tous les prérequis installés :

### Étape 1 : Vérifier les prérequis

```powershell
php --version      # Doit afficher PHP 8.1+
composer --version # Doit afficher Composer
mysql --version    # Doit afficher MySQL
node --version     # Déjà installé ✅
npm --version      # Déjà installé ✅
```

### Étape 2 : Installer les dépendances PHP

```powershell
composer install
```

### Étape 3 : Configurer l'environnement

```powershell
# Copier le fichier d'exemple
Copy-Item .env.example .env

# Ou créer manuellement si .env.example n'existe pas
```

### Étape 4 : Configurer le fichier .env

Ouvrez `.env` et modifiez :

```env
APP_NAME=WEGA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wega
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe_mysql

# Si Redis n'est pas installé, utilisez 'file'
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Si Redis est installé
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis
# REDIS_HOST=127.0.0.1
# REDIS_PORT=6379
```

### Étape 5 : Créer la base de données

```powershell
# Se connecter à MySQL
mysql -u root -p

# Dans MySQL, créer la base de données
CREATE DATABASE wega CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Étape 6 : Générer la clé d'application

```powershell
php artisan key:generate
```

### Étape 7 : Exécuter les migrations

```powershell
php artisan migrate
```

### Étape 8 : Optionnel - Exécuter les seeders

```powershell
php artisan db:seed
```

### Étape 9 : Installer les dépendances NPM

```powershell
npm install
```

### Étape 10 : Compiler les assets (Développement)

```powershell
# Dans un terminal séparé, gardez cette commande en cours d'exécution
npm run dev
```

### Étape 11 : Démarrer le serveur Laravel

```powershell
# Dans un autre terminal
php artisan serve
```

### Étape 12 : Accéder à l'application

Ouvrez votre navigateur : **http://localhost:8000**

## 🔧 Commandes Utiles

### Vider le cache
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Recréer la base de données
```powershell
php artisan migrate:fresh --seed
```

### Voir les routes
```powershell
php artisan route:list
```

## ⚠️ Dépannage

### Erreur "Class 'PDO' not found"
- Activez l'extension `pdo_mysql` dans `php.ini`
- Redémarrez le serveur web/PHP

### Erreur de connexion MySQL
- Vérifiez que MySQL est démarré
- Vérifiez les identifiants dans `.env`
- Testez la connexion : `mysql -u root -p`

### Erreur "Vite manifest not found"
- Exécutez `npm run dev` dans un terminal séparé
- Ou compilez pour production : `npm run build`

### Port 8000 déjà utilisé
```powershell
# Utiliser un autre port
php artisan serve --port=8080
```

## 📝 Notes

- **Redis** : Optionnel pour le développement. Utilisez `file` si vous ne voulez pas l'installer.
- **MySQL** : Assurez-vous que le service MySQL est démarré avant de lancer l'application.
- **Vite** : En développement, gardez `npm run dev` en cours d'exécution dans un terminal séparé.

## 🎯 Prochaines Étapes

Une fois l'application lancée :
1. Créez un compte utilisateur via `/register`
2. Connectez-vous via `/login`
3. Commencez à utiliser l'application !

