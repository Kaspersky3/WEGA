# Variables d'environnement Docker

Voici les variables d'environnement à configurer dans votre fichier `.env` pour utiliser Docker :

## Variables d'application

```env
APP_NAME=WEGA
APP_ENV=local
APP_KEY=base64:... (généré automatiquement)
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## Variables de base de données

```env
DB_CONNECTION=mysql
DB_HOST=db                    # Nom du service dans docker-compose.yml
DB_PORT=3306
DB_DATABASE=wega
DB_USERNAME=wega
DB_PASSWORD=password
```

**Note importante** : `DB_HOST` doit être `db` (le nom du service MySQL dans docker-compose.yml), pas `localhost` ou `127.0.0.1`.

## Variables Redis

```env
REDIS_HOST=redis              # Nom du service dans docker-compose.yml
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Note importante** : `REDIS_HOST` doit être `redis` (le nom du service Redis dans docker-compose.yml).

## Variables de cache et session

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

## Variables Docker Compose (optionnelles)

Vous pouvez aussi définir ces variables dans votre `.env` pour personnaliser la configuration Docker :

```env
DB_ROOT_PASSWORD=rootpassword
DB_DATABASE=wega
DB_USERNAME=wega
DB_PASSWORD=password
```

Ces variables sont utilisées par `docker-compose.yml` pour configurer MySQL.


