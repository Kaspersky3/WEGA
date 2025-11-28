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
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('reference')->unique()->after('id');
            $table->foreignId('user_id')->nullable()->after('reference')->constrained()->nullOnDelete();
            $table->dateTime('inventory_date')->after('user_id')->useCurrent();
            $table->enum('status', ['draft', 'validated'])->default('validated')->after('inventory_date');
            $table->unsignedBigInteger('total_stock_theorique')->default(0)->after('notes');
            $table->unsignedBigInteger('total_stock_reel')->default(0)->after('total_stock_theorique');
            $table->bigInteger('total_gap_units')->default(0)->after('total_stock_reel');
            $table->decimal('total_gap_value', 14, 2)->default(0)->after('total_gap_units');
            $table->json('filters_snapshot')->nullable()->after('total_gap_value');

            $table->index('inventory_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['inventory_date']);
            $table->dropIndex(['status']);

            $table->dropColumn([
                'reference',
                'user_id',
                'inventory_date',
                'status',
                'total_stock_theorique',
                'total_stock_reel',
                'total_gap_units',
                'total_gap_value',
                'filters_snapshot',
            ]);
        });
    }
};





