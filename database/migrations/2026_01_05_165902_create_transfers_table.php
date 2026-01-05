<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('lieu_source', ['Stock', 'Boutique 1', 'Boutique 2']);
            $table->enum('lieu_destination', ['Stock', 'Boutique 1', 'Boutique 2']);
            $table->unsignedInteger('quantite_gros')->default(0);
            $table->unsignedInteger('quantite_detail')->default(0);
            $table->dateTime('date_transfert');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('date_transfert');
            $table->index('lieu_source');
            $table->index('lieu_destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
