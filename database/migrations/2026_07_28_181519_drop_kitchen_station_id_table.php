<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ejecuta esta migración SOLO después de confirmar que la tabla
 * kitchen_station_product quedó bien poblada (compara conteos
 * contra los productos que antes tenían kitchen_station_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['kitchen_station_id']);
            $table->dropColumn('kitchen_station_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('kitchen_station_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }
};