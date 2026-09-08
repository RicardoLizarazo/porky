<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_inventory_item', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('supplier_sku')->nullable();

            // último costo pagado, en la unidad base del producto
            $table->decimal('last_price', 14, 4)->nullable();

            $table->timestamps();

            $table->unique(['supplier_id', 'inventory_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_inventory_item');
    }
};
