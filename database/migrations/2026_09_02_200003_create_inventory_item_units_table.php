<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained()
                ->restrictOnDelete();

            // cuántas unidades base trae 1 de esta unidad (ej. 1 Bulto = 100 Libras)
            $table->decimal('factor_to_base', 14, 6);

            $table->timestamps();

            $table->unique(['inventory_item_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_units');
    }
};
