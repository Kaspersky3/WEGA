# Script d'initialisation Docker pour Windows PowerShell

Write-Host "Initialisation du projet WEGA avec Docker..." -ForegroundColor Green

# Vérifier si Docker est en cours d'exécution
Write-Host "Vérification de Docker..." -ForegroundColor Yellow
try {
    docker ps | Out-Null
    Write-Host "Docker est en cours d'exécution." -ForegroundColor Green
} catch {
    Write-Host "Erreur: Docker n'est pas en cours d'exécution. Veuillez démarrer Docker Desktop." -ForegroundColor Red
    exit 1
}

# Attendre que MySQL soit prêt
Write-Host "Attente de la base de données..." -ForegroundColor Yellow
$maxAttempts = 30
$attempt = 0
$dbReady = $false

while ($attempt -lt $maxAttempts -and -not $dbReady) {
    try {
        docker-compose exec -T db mysqladmin ping -h localhost --silent 2>$null
        if ($LASTEXITCODE -eq 0) {
            $dbReady = $true
            Write-Host "MySQL est prêt!" -ForegroundColor Green
        }
    } catch {
        Start-Sleep -Seconds 2
        $attempt++
        Write-Host "Tentative $attempt/$maxAttempts..." -ForegroundColor Yellow
    }
}

if (-not $dbReady) {
    Write-Host "Erreur: MySQL n'a pas démarré dans le délai imparti." -ForegroundColor Red
    exit 1
}

# Installer les dépendances Composer si nécessaire
if (-not (Test-Path "vendor")) {
    Write-Host "Installation des dépendances Composer..." -ForegroundColor Yellow
    docker-compose exec -T app composer install
}

# Installer les dépendances NPM si nécessaire
if (-not (Test-Path "node_modules")) {
    Write-Host "Installation des dépendances NPM..." -ForegroundColor Yellow
    docker-compose exec -T node npm install
}

# Copier .env si nécessaire
if (-not (Test-Path ".env")) {
    Write-Host "Création du fichier .env..." -ForegroundColor Yellow
    if (Test-Path "env.docker.example") {
        Copy-Item "env.docker.example" ".env"
    } elseif (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
    } else {
        "APP_KEY=" | Out-File -FilePath ".env" -Encoding utf8
    }
}

# Générer la clé d'application
Write-Host "Génération de la clé d'application..." -ForegroundColor Yellow
docker-compose exec -T app php artisan key:generate --force

# Exécuter les migrations
Write-Host "Exécution des migrations..." -ForegroundColor Yellow
docker-compose exec -T app php artisan migrate --force

Write-Host "`nInitialisation terminée!" -ForegroundColor Green
Write-Host "L'application est accessible sur http://localhost:8000" -ForegroundColor Cyan

