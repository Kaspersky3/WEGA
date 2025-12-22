<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur par défaut
        // Identifiants pour la première connexion
        User::firstOrCreate(
            ['email' => 'admin@wega.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin123'), // Mot de passe par défaut pour admin
                'role' => User::ROLE_ADMIN, // Utiliser la constante du modèle
            ]
        );

        // Créer un utilisateur standard par défaut (pour tests)
        User::firstOrCreate(
            ['email' => 'user@wega.com'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('user123'), // Mot de passe par défaut pour user
                'role' => User::ROLE_USER, // Utiliser la constante du modèle
            ]
        );

        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ Utilisateurs créés avec succès !');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔐 COMPTE ADMINISTRATEUR (Première connexion)');
        $this->command->info('   Email    : admin@wega.com');
        $this->command->info('   Mot de passe : admin123');
        $this->command->info('   Type     : Administrateur');
        $this->command->info('');
        $this->command->info('👤 COMPTE UTILISATEUR (Pour tests)');
        $this->command->info('   Email    : user@wega.com');
        $this->command->info('   Mot de passe : user123');
        $this->command->info('   Type     : Utilisateur');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT : Changez les mots de passe par défaut en production !');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}

