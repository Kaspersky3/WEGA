<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pour MySQL/MariaDB, on doit utiliser ALTER TABLE pour modifier l'enum
        // On remplace l'ancien enum par le nouveau avec les trois options
        DB::statement("ALTER TABLE products MODIFY COLUMN lieu ENUM('Stock', 'Boutique 1', 'Boutique 2') DEFAULT 'Stock'");
        
        // Mettre à jour les anciennes valeurs "Boutique" en "Boutique 1"
        DB::table('products')
            ->where('lieu', 'Boutique')
            ->update(['lieu' => 'Boutique 1']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convertir "Boutique 1" et "Boutique 2" en "Boutique" avant de revenir à l'ancien enum
        DB::table('products')
            ->whereIn('lieu', ['Boutique 1', 'Boutique 2'])
            ->update(['lieu' => 'Boutique']);
        
        // Revenir à l'ancien enum
        DB::statement("ALTER TABLE products MODIFY COLUMN lieu ENUM('Stock', 'Boutique') DEFAULT 'Stock'");
    }
};
