# Documentation Complète du Projet WEGA

## 📋 Vue d'ensemble

**WEGA** est une application web de gestion d'inventaire et de stock développée avec **Laravel 10**. Elle permet de gérer des produits avec des unités de vente en gros et en détail, de suivre les approvisionnements, d'effectuer des inventaires et d'analyser les performances commerciales.

## 🏗️ Architecture du Projet

### Stack Technologique

- **Backend** : Laravel 10 (PHP 8.1+)
- **Base de données** : MySQL 8.0
- **Cache/Sessions** : Redis
- **Frontend** : Blade Templates + Vite (assets)
- **Export** : OpenSpout (CSV/XLSX)
- **Containerisation** : Docker & Docker Compose
- **Déploiement** : Render.com (avec support Docker)

### Structure des Répertoires

```
WEGA/
├── app/
│   ├── Console/           # Commandes Artisan
│   ├── Exceptions/        # Gestion des erreurs
│   ├── Http/
│   │   ├── Controllers/   # Contrôleurs MVC
│   │   ├── Middleware/    # Middlewares (auth, CSRF, etc.)
│   │   └── Requests/     # Form Requests (validation)
│   ├── Models/           # Modèles Eloquent
│   ├── Policies/         # Politiques d'autorisation
│   ├── Providers/        # Service Providers
│   └── Services/         # Services métier (logique métier)
├── bootstrap/            # Bootstrap de l'application
├── config/              # Fichiers de configuration
├── database/
│   ├── factories/       # Factories pour les tests
│   ├── migrations/      # Migrations de base de données
│   └── seeders/         # Seeders pour données de test
├── docker/              # Configuration Docker
├── public/              # Point d'entrée web
├── resources/
│   ├── css/             # Styles CSS
│   ├── js/              # JavaScript
│   └── views/           # Vues Blade
├── routes/              # Définition des routes
├── storage/             # Fichiers de logs, cache, uploads
└── tests/               # Tests automatisés
```

## 🎯 Fonctionnalités Principales

### 1. **Gestion des Produits**

- **Création de produits** avec :
  - Code produit auto-généré (format: P001, P002, etc.)
  - Catégorie et libellé
  - Type de produit : Gros, Détail, ou Les deux
  - Lieu : Stock ou Boutique
  - Prix d'achat et de vente (gros et/ou détail)
  - Stocks (gros et/ou détail)
  - Unités personnalisables (ex: "Carton", "Pièce", etc.)
  - Taux de conversion entre gros et détail

- **Liste des produits** avec recherche et filtres
- **Fiche détaillée** d'un produit avec historique des approvisionnements et ventes

### 2. **Gestion des Approvisionnements**

- Enregistrement d'approvisionnements avec :
  - Type : Gros, Détail, ou Les deux
  - Quantités (gros et/ou détail)
  - Date d'approvisionnement
  - Notes optionnelles
- Mise à jour automatique des stocks du produit
- Calcul automatique du montant total des achats

### 3. **Gestion des Inventaires**

- **Création d'inventaire** :
  - Filtrage par catégorie, recherche, référence
  - Saisie des stocks réels (gros et détail)
  - Calcul automatique des écarts (théorique vs réel)
  - Valorisation des écarts en FCFA
  - Génération de référence unique (INV-YYYYMMDD-XXXX)

- **Historique des inventaires** :
  - Liste avec filtres (statut, dates, recherche)
  - Tri par date, écart, référence
  - Pagination

- **Détails d'un inventaire** :
  - Vue complète avec tous les produits
  - Écarts par produit
  - Totaux globaux

- **Export** :
  - Export de l'historique (CSV/XLSX)
  - Export des détails d'un inventaire (CSV/XLSX)

### 4. **Analytics & Rapports**

- **Tableau de bord analytique** :
  - Top produits les plus vendus (quantité)
  - Top produits les plus rentables (bénéfice)
  - Filtres par catégorie, période (jour, semaine, mois, année, personnalisée)
  - Limite de résultats (5, 10, 20)

- **Export des analyses** :
  - Export CSV/XLSX des tops ventes
  - Export CSV/XLSX des tops rentables

### 5. **Authentification**

- Inscription/Connexion
- Réinitialisation de mot de passe
- Changement de mot de passe
- Gestion des rôles utilisateurs (user, admin, etc.)

## 📊 Modèles de Données

### Product (Produit)
- `code_produit` : Code unique auto-généré
- `categorie`, `libelle`
- `type_produit` : Gros, Détail, Les deux
- `lieu` : Stock, Boutique
- Stocks : `stock_gros`, `stock_detail`, `stock_boutique_gros`, `stock_boutique_detail`
- Prix : `prix_achat_gros`, `prix_vente_gros`, `prix_achat_detail`, `prix_vente_detail`
- Unités : `bulk_unit_label`, `detail_unit_label`, `units_per_bulk`
- Statistiques : `quantite_vendue_gros`, `quantite_vendue_detail`, `montant_total_ventes`, `montant_total_achats`

### Supply (Approvisionnement)
- `product_id`, `type_approvisionnement`
- `quantite_gros`, `quantite_detail`
- `date_approvisionnement`, `notes`

### Inventory (Inventaire)
- `reference` : Référence unique (INV-YYYYMMDD-XXXX)
- `user_id`, `inventory_date`
- `status`, `year`, `month`, `month_name`
- Totaux : `total_ca`, `total_cost`, `total_margin`
- Stocks : `total_stock_theorique`, `total_stock_reel`
- Écarts : `total_gap_units`, `total_gap_value`
- `notes`, `filters_snapshot`

### InventoryDetail (Détail d'inventaire)
- `inventory_id`, `product_id`
- Stocks théoriques : `stock_theorique_gros`, `stock_theorique_detail`, `stock_theorique_total`
- Stocks réels : `stock_reel_gros`, `stock_reel_detail`, `stock_reel_total`
- `conversion_rate`
- Écarts : `gap_units`, `gap_value`, `unit_purchase_price`

### Sale (Vente)
- `product_id`, `type_vente` (Gros/Détail)
- `quantite`, `prix_unitaire`, `montant_total`
- `date_vente`, `notes`

### User (Utilisateur)
- `name`, `email`, `password`, `role`

## 🔧 Services Métier

### InventoryService
- `getProductsSnapshot()` : Récupère les produits avec filtres pour l'inventaire
- `storeInventory()` : Enregistre un inventaire complet avec calculs automatiques

### AnalyticsService
- `getTopSelling()` : Top produits les plus vendus (avec cache)
- `getTopProfitable()` : Top produits les plus rentables (avec cache)
- Gestion des périodes (jour, semaine, mois, année, personnalisée)

### ExportService
- `download()` : Export CSV/XLSX générique
- Support de l'encodage UTF-8 avec BOM pour Excel

## 🚀 Démarrage du Projet

### Option 1 : Avec Docker (Recommandé)

#### Prérequis
- Docker
- Docker Compose

#### Étapes

1. **Initialisation automatique (Windows PowerShell)**
   ```powershell
   .\docker-init.ps1
   ```

   **Ou manuellement (Linux/Mac)**
   ```bash
   chmod +x docker/init.sh
   ./docker/init.sh
   ```

2. **Copier le fichier d'environnement**
   ```bash
   # Windows PowerShell
   Copy-Item env.docker.example .env
   
   # Linux/Mac
   cp env.docker.example .env
   ```

3. **Démarrer les conteneurs**
   ```bash
   docker-compose up -d
   ```

4. **Générer la clé d'application**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. **Exécuter les migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Optionnel : Exécuter les seeders**
   ```bash
   docker-compose exec app php artisan db:seed
   ```

7. **Installer les dépendances NPM (pour Vite)**
   ```bash
   docker-compose exec node npm install
   ```

8. **Compiler les assets (développement)**
   ```bash
   docker-compose exec node npm run dev
   ```

9. **Accéder à l'application**
   - Application : http://localhost:8000
   - Vite Dev Server : http://localhost:5173

### Option 2 : Installation Locale (sans Docker)

#### Prérequis
- PHP 8.1+
- Composer
- MySQL 8.0
- Redis
- Node.js 18+ et NPM

#### Étapes

1. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

2. **Copier le fichier d'environnement**
   ```bash
   cp .env.example .env
   ```

3. **Configurer `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wega
   DB_USERNAME=root
   DB_PASSWORD=votre_mot_de_passe

   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   ```

4. **Générer la clé d'application**
   ```bash
   php artisan key:generate
   ```

5. **Exécuter les migrations**
   ```bash
   php artisan migrate
   ```

6. **Optionnel : Exécuter les seeders**
   ```bash
   php artisan db:seed
   ```

7. **Installer les dépendances NPM**
   ```bash
   npm install
   ```

8. **Compiler les assets (développement)**
   ```bash
   npm run dev
   ```

9. **Démarrer le serveur de développement**
   ```bash
   php artisan serve
   ```

10. **Accéder à l'application**
    - Application : http://localhost:8000

## 📝 Routes Principales

### Routes Publiques
- `/` : Page d'accueil
- `/login` : Connexion
- `/register` : Inscription
- `/forgot-password` : Mot de passe oublié
- `/reset-password/{token}` : Réinitialisation

### Routes Produits
- `GET /products` : Liste des produits
- `GET /products/create` : Formulaire de création
- `POST /products` : Créer un produit
- `GET /products/{id}` : Détails d'un produit

### Routes Approvisionnements
- `GET /supplies/create` : Formulaire d'approvisionnement
- `POST /supplies` : Enregistrer un approvisionnement
- `GET /api/products/search` : Recherche de produits (autocomplete)

### Routes Inventaires
- `GET /inventories` : Liste des inventaires
- `GET /inventories/create` : Créer un inventaire
- `POST /inventories` : Enregistrer un inventaire
- `GET /inventories/{inventory}` : Détails d'un inventaire
- `GET /inventories/history/export` : Export de l'historique
- `GET /inventories/{inventory}/export/{format}` : Export des détails (CSV/XLSX)

### Routes Analytics
- `GET /analytics` : Tableau de bord
- `GET /analytics/data` : Données JSON (AJAX)
- `GET /analytics/export/{type}/{format}` : Export (top-selling/top-profitable)

## 🐳 Configuration Docker

### Services Docker

1. **app** : PHP-FPM 8.2 (Laravel)
2. **nginx** : Serveur web Nginx
3. **db** : MySQL 8.0
4. **redis** : Cache Redis
5. **node** : Node.js 18 (pour Vite en développement)

### Variables d'Environnement Docker

⚠️ **Important** : Dans `.env`, utilisez les noms de services Docker :
- `DB_HOST=db` (pas `localhost`)
- `REDIS_HOST=redis` (pas `localhost`)

## 🌐 Déploiement sur Render

### Déploiement Rapide (5 minutes)

1. **Préparer le code**
   ```bash
   git add .
   git commit -m "Prêt pour le déploiement Render"
   git push
   ```

2. **Créer un compte Render** : https://render.com

3. **Déployer avec Blueprint**
   - Dans Render Dashboard : **"New +"** → **"Blueprint"**
   - Sélectionner votre dépôt
   - Render détectera automatiquement `render.yaml`
   - Cliquer sur **"Apply"**

4. **Configurer les variables d'environnement**
   Dans le service web (wega-app), ajouter :
   ```env
   APP_NAME=WEGA
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votre-app.onrender.com
   ```

5. **Générer la clé d'application**
   Dans le **Shell** du service web :
   ```bash
   php artisan key:generate
   ```
   Copier la clé et l'ajouter comme `APP_KEY` dans les variables d'environnement.

6. **Exécuter les migrations**
   ```bash
   php artisan migrate --force
   ```

### Configuration Render

Le fichier `render.yaml` configure automatiquement :
- Service web (Docker)
- Base de données MySQL
- Redis
- Variables d'environnement
- Injection automatique de `DATABASE_URL` et `REDIS_URL`

## 🔐 Sécurité

- Authentification Laravel avec Sanctum
- Protection CSRF
- Validation des données avec Form Requests
- Politiques d'autorisation (Policies)
- Hashage des mots de passe (bcrypt)
- Protection des routes avec middleware `auth`

## 📦 Dépendances Principales

### PHP (composer.json)
- `laravel/framework` : ^10.10
- `laravel/sanctum` : ^3.3
- `openspout/openspout` : ^4.25 (export CSV/XLSX)

### JavaScript (package.json)
- `vite` : ^5.0.0
- `laravel-vite-plugin` : ^1.0.0
- `axios` : ^1.6.4

## 🧪 Tests

Les tests sont situés dans `tests/` :
- `Feature/` : Tests d'intégration
- `Unit/` : Tests unitaires

Exécuter les tests :
```bash
# Avec Docker
docker-compose exec app php artisan test

# Local
php artisan test
```

## 📚 Commandes Utiles

### Artisan
```bash
# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimisation production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Docker
```bash
# Voir les logs
docker-compose logs -f

# Redémarrer un service
docker-compose restart app

# Accéder au shell du conteneur
docker-compose exec app bash

# Reconstruire les images
docker-compose build
docker-compose up -d
```

## 🎨 Interface Utilisateur

- **Framework CSS** : Bootstrap 5.3
- **Templates** : Blade (Laravel)
- **Assets** : Vite pour le build
- **Responsive** : Design adaptatif mobile/desktop

## 📈 Fonctionnalités Avancées

### Calculs Automatiques
- Conversion automatique entre unités gros/détail
- Calcul des écarts d'inventaire
- Valorisation des écarts en FCFA
- Mise à jour automatique des stocks après inventaire

### Cache
- Cache Redis pour les sessions
- Cache des requêtes analytiques (15 minutes)
- Cache de configuration en production

### Export
- Export CSV avec encodage UTF-8 BOM (compatible Excel)
- Export XLSX (Excel)
- Export de l'historique des inventaires
- Export des analyses (tops ventes/rentables)

## 🔄 Workflow de Développement

1. **Développement local** : Docker Compose
2. **Tests** : PHPUnit
3. **Versioning** : Git
4. **Déploiement** : Render.com (automatique via Git)

## 📞 Support

Pour plus d'informations :
- Documentation Laravel : https://laravel.com/docs
- Documentation Render : https://render.com/docs
- Fichiers de documentation dans le projet :
  - `DEPLOY_RENDER.md` : Guide complet de déploiement
  - `QUICK_START_RENDER.md` : Démarrage rapide
  - `docker/README.md` : Documentation Docker

---

**Dernière mise à jour** : Analyse complète du projet WEGA


