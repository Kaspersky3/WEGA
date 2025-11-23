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
        Schema::table('products', function (Blueprint $table) {
            $table->string('bulk_unit_label')->default('Carton')->after('stock_boutique_detail');
            $table->string('detail_unit_label')->default('Unité')->after('bulk_unit_label');
            $table->unsignedInteger('units_per_bulk')->default(1)->after('detail_unit_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['bulk_unit_label', 'detail_unit_label', 'units_per_bulk']);
        });
    }
};



