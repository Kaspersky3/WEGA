<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin 
                            {--email=admin@wega.com : Email de l\'administrateur}
                            {--password=admin123 : Mot de passe de l\'administrateur}
                            {--name=Administrateur : Nom de l\'administrateur}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer ou mettre à jour un utilisateur administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Vérifier si l'utilisateur existe déjà
        $user = User::where('email', $email)->first();

        if ($user) {
            // Mettre à jour l'utilisateur existant
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
            ]);

            $this->info("✅ Utilisateur administrateur mis à jour avec succès !");
        } else {
            // Créer un nouvel utilisateur
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
            ]);

            $this->info("✅ Utilisateur administrateur créé avec succès !");
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🔐 COMPTE ADMINISTRATEUR');
        $this->info('   Email        : ' . $email);
        $this->info('   Mot de passe  : ' . $password);
        $this->info('   Nom           : ' . $name);
        $this->info('   Rôle          : Administrateur');
        $this->info('═══════════════════════════════════════════════════════');
        $this->warn('');
        $this->warn('⚠️  IMPORTANT : Changez le mot de passe en production !');

        return Command::SUCCESS;
    }
}



