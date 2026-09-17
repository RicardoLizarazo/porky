<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_item_id')
                ->constrained()
                ->restrictOnDelete();

            // purchase_in | purchase_return_out | waste_out | employee_consumption_out | adjustment
            $table->string('type', 30);

            // en unidad base del producto, con signo (+entra / -sale)
            $table->decimal('quantity', 14, 4);

            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->decimal('balance_after', 14, 4);

            $table->text('reason_note')->nullable();

            $table->nullableMorphs('reference');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['inventory_item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
