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
        User::firstOrCreate(
            ['email' => 'admin@wega.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Créer un utilisateur standard par défaut
        User::firstOrCreate(
            ['email' => 'user@wega.com'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('Admin: admin@wega.com / password');
        $this->command->info('User: user@wega.com / password');
    }
}

