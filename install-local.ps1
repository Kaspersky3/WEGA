# Script d'installation locale pour WEGA
# À exécuter après avoir installé PHP, Composer et MySQL

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Installation Locale - WEGA" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier PHP
Write-Host "Vérification de PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php --version 2>&1 | Select-Object -First 1
    if ($phpVersion -match "PHP (\d+)\.(\d+)") {
        $major = [int]$matches[1]
        $minor = [int]$matches[2]
        if ($major -ge 8 -and $minor -ge 1) {
            Write-Host "✓ PHP $major.$minor détecté" -ForegroundColor Green
        } else {
            Write-Host "✗ PHP 8.1+ requis (version détectée: $major.$minor)" -ForegroundColor Red
            exit 1
        }
    }
} catch {
    Write-Host "✗ PHP n'est pas installé ou n'est pas dans le PATH" -ForegroundColor Red
    Write-Host "  Installez PHP 8.1+ et ajoutez-le au PATH" -ForegroundColor Yellow
    exit 1
}

# Vérifier Composer
Write-Host "Vérification de Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1 | Select-Object -First 1
    Write-Host "✓ Composer détecté: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Composer n'est pas installé ou n'est pas dans le PATH" -ForegroundColor Red
    Write-Host "  Installez Composer depuis https://getcomposer.org/" -ForegroundColor Yellow
    exit 1
}

# Vérifier Node.js
Write-Host "Vérification de Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version
    Write-Host "✓ Node.js $nodeVersion détecté" -ForegroundColor Green
} catch {
    Write-Host "✗ Node.js n'est pas installé" -ForegroundColor Red
    exit 1
}

# Vérifier NPM
Write-Host "Vérification de NPM..." -ForegroundColor Yellow
try {
    $npmVersion = npm --version
    Write-Host "✓ NPM $npmVersion détecté" -ForegroundColor Green
} catch {
    Write-Host "✗ NPM n'est pas installé" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Tous les prérequis sont installés !" -ForegroundColor Green
Write-Host ""

# Créer .env si nécessaire
if (-not (Test-Path ".env")) {
    Write-Host "Création du fichier .env..." -ForegroundColor Yellow
    if (Test-Path "env.local.example") {
        Copy-Item "env.local.example" ".env"
        Write-Host "✓ Fichier .env créé à partir de env.local.example" -ForegroundColor Green
        Write-Host "  ⚠️  N'oubliez pas de configurer DB_PASSWORD dans .env" -ForegroundColor Yellow
    } elseif (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "✓ Fichier .env créé à partir de .env.example" -ForegroundColor Green
        Write-Host "  ⚠️  N'oubliez pas de configurer DB_PASSWORD dans .env" -ForegroundColor Yellow
    } else {
        Write-Host "✗ Fichier env.local.example ou .env.example introuvable" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✓ Fichier .env existe déjà" -ForegroundColor Green
}

# Installer les dépendances Composer
Write-Host ""
Write-Host "Installation des dépendances Composer..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Erreur lors de l'installation des dépendances Composer" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Dépendances Composer installées" -ForegroundColor Green

# Générer la clé d'application
Write-Host ""
Write-Host "Génération de la clé d'application..." -ForegroundColor Yellow
php artisan key:generate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Erreur lors de la génération de la clé" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Clé d'application générée" -ForegroundColor Green

# Installer les dépendances NPM
Write-Host ""
Write-Host "Installation des dépendances NPM..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Erreur lors de l'installation des dépendances NPM" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Dépendances NPM installées" -ForegroundColor Green

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Installation terminée !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Prochaines étapes :" -ForegroundColor Yellow
Write-Host "1. Créez la base de données MySQL :" -ForegroundColor White
Write-Host "   mysql -u root -p" -ForegroundColor Gray
Write-Host "   CREATE DATABASE wega CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Gray
Write-Host "   EXIT;" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Configurez DB_PASSWORD dans le fichier .env" -ForegroundColor White
Write-Host ""
Write-Host "3. Exécutez les migrations :" -ForegroundColor White
Write-Host "   php artisan migrate" -ForegroundColor Gray
Write-Host ""
Write-Host "4. (Optionnel) Exécutez les seeders :" -ForegroundColor White
Write-Host "   php artisan db:seed" -ForegroundColor Gray
Write-Host ""
Write-Host "5. Compilez les assets (dans un terminal séparé) :" -ForegroundColor White
Write-Host "   npm run dev" -ForegroundColor Gray
Write-Host ""
Write-Host "6. Démarrez le serveur (dans un autre terminal) :" -ForegroundColor White
Write-Host "   php artisan serve" -ForegroundColor Gray
Write-Host ""
Write-Host "7. Accédez à l'application :" -ForegroundColor White
Write-Host "   http://localhost:8000" -ForegroundColor Cyan
Write-Host ""

