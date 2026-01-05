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
        // ÉTAPE 1: Modifier temporairement l'enum pour inclure toutes les valeurs possibles
        // Cela permet de modifier les données existantes sans erreur
        DB::statement("ALTER TABLE products MODIFY COLUMN lieu ENUM('Stock', 'Boutique', 'Boutique 1', 'Boutique 2') DEFAULT 'Stock'");
        
        // ÉTAPE 2: Mettre à jour les anciennes valeurs "Boutique" en "Boutique 1"
        DB::table('products')
            ->where('lieu', 'Boutique')
            ->update(['lieu' => 'Boutique 1']);
        
        // ÉTAPE 3: Maintenant on peut modifier l'enum pour ne garder que les nouvelles valeurs
        DB::statement("ALTER TABLE products MODIFY COLUMN lieu ENUM('Stock', 'Boutique 1', 'Boutique 2') DEFAULT 'Stock'");
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
