<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchen_station_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('kitchen_station_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['product_id', 'kitchen_station_id']);
        });

        // Migramos la asignación actual (uno a uno) hacia la tabla pivote,
        // así ningún producto existente pierde su estación al pasar al
        // esquema nuevo.
        DB::table('products')
            ->whereNotNull('kitchen_station_id')
            ->select('id', 'kitchen_station_id')
            ->orderBy('id')
            ->chunk(200, function ($products) {

                $rows = $products->map(fn ($product) => [
                    'product_id' => $product->id,
                    'kitchen_station_id' => $product->kitchen_station_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                DB::table('kitchen_station_product')->insert($rows);

            });

        // Dejamos la columna vieja por ahora (no la borramos en este paso)
        // para poder verificar los datos migrados antes de eliminarla en
        // una migración posterior.
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_station_product');
    }
};