<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->nullable()->index();

            // raw_material | resale | packaging | cleaning
            $table->string('type', 20);

            $table->foreignId('base_unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->decimal('stock', 14, 4)->default(0);
            $table->decimal('average_cost', 14, 4)->default(0);
            $table->decimal('min_stock', 14, 4)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
