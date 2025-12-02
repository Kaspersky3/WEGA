# Comptes Utilisateurs par Défaut

## 📋 Utilisateurs créés par le seeder

Après avoir exécuté le seeder, deux comptes utilisateurs sont disponibles :

### 👤 Administrateur
- **Email** : `admin@wega.com`
- **Mot de passe** : `password`
- **Rôle** : `admin`

### 👤 Utilisateur Standard
- **Email** : `user@wega.com`
- **Mot de passe** : `password`
- **Rôle** : `user`

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
- ✅ Formulaire de connexion avec email et mot de passe
- ✅ Case à cocher "Se souvenir de moi"
- ✅ Lien "Mot de passe oublié"
- ✅ Lien vers la page d'inscription
- ✅ Design moderne et responsive
- ✅ Gestion des erreurs de connexion

### Après connexion :
- Redirection automatique vers `/inventories` (liste des inventaires)
- Session utilisateur créée
- Accès à toutes les fonctionnalités de l'application

## 📝 Créer un nouveau compte

Vous pouvez également créer un nouveau compte via l'interface d'inscription :
- **URL** : `/register`
- Formulaire d'inscription disponible
- Création automatique avec le rôle `user`

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

