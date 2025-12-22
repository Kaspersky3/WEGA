# Comptes Utilisateurs par Défaut

## 📋 Utilisateurs créés par le seeder

Après avoir exécuté le seeder, deux comptes utilisateurs sont disponibles :

### 🔐 Administrateur (Première connexion)
- **Email** : `admin@wega.com`
- **Mot de passe** : `admin123`
- **Rôle** : `admin`
- **Accès** : Toutes les fonctionnalités + gestion des utilisateurs

### 👤 Utilisateur Standard (Pour tests)
- **Email** : `user@wega.com`
- **Mot de passe** : `user123`
- **Rôle** : `user`
- **Accès** : Fonctionnalités métier uniquement (produits, inventaires, etc.)

## 🚀 Création des utilisateurs

### Option 1 : Avec le seeder (Recommandé)

```bash
# Exécuter uniquement le seeder UserSeeder
php artisan db:seed --class=UserSeeder

# Ou exécuter tous les seeders
php artisan db:seed
```

### Option 2 : Avec Docker

```bash
# Exécuter uniquement le seeder UserSeeder
docker-compose exec app php artisan db:seed --class=UserSeeder

# Ou exécuter tous les seeders
docker-compose exec app php artisan db:seed
```

## 🔐 Interface de Connexion

L'interface de connexion est accessible à l'adresse : **`/login`**

### Fonctionnalités disponibles :
- ✅ **Sélection du type d'utilisateur** : Choisir entre "Administrateur" ou "Utilisateur"
- ✅ Formulaire de connexion avec email et mot de passe
- ✅ Case à cocher "Se souvenir de moi"
- ✅ Lien "Mot de passe oublié"
- ✅ Design moderne et responsive
- ✅ Gestion des erreurs de connexion
- ✅ Validation du type d'utilisateur (correspondance avec le compte)

### ⚠️ Important
- **Pas d'inscription publique** : Seuls les administrateurs peuvent créer des comptes utilisateurs
- **Type d'utilisateur requis** : Vous devez sélectionner le type d'utilisateur lors de la connexion
- **Vérification automatique** : Le système vérifie que le type sélectionné correspond au compte

### Après connexion :
- Redirection automatique vers `/inventories` (liste des inventaires)
- Session utilisateur créée
- Accès aux fonctionnalités selon le rôle

## 📝 Créer un nouveau compte utilisateur

**Seuls les administrateurs peuvent créer des comptes utilisateurs.**

Pour créer un nouveau compte :
1. Se connecter en tant qu'administrateur
2. Aller dans **Administration** → **Utilisateurs**
3. Cliquer sur **Nouvel Utilisateur**
4. Remplir le formulaire et valider

**L'inscription publique (`/register`) a été désactivée pour des raisons de sécurité.**

## ⚠️ Sécurité

**Important** : Changez les mots de passe par défaut en production !

Pour changer un mot de passe :
1. Connectez-vous avec le compte
2. Allez dans les paramètres : `/settings/password`
3. Changez le mot de passe

## 🔧 Commandes utiles

### Créer un utilisateur manuellement via Tinker

```bash
php artisan tinker
```

Puis dans Tinker :
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nom Utilisateur',
    'email' => 'email@example.com',
    'password' => Hash::make('mot_de_passe'),
    'role' => 'user', // ou 'admin'
]);
```

### Vérifier les utilisateurs existants

```bash
php artisan tinker
```

Puis :
```php
use App\Models\User;
User::all(['id', 'name', 'email', 'role']);
```

