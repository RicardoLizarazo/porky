<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->restrictOnDelete();

            // unidad en la que se compró esta línea (ej. Bulto)
            $table->foreignId('unit_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 4);

            // cantidad convertida a la unidad base del producto (quantity * factor_to_base)
            $table->decimal('base_quantity', 14, 4);

            $table->decimal('subtotal', 14, 4);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
