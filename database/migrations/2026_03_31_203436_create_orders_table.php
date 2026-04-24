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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            // Cliente (frontend)
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();

            // Usuario admin que gestiona el pedido
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // 🔥 Domiciliario asignado
            $table->foreignId('delivery_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Tipo de pedido (domicilio, recoger, mesa)
            $table->foreignId('type_id')
                  ->constrained('type_orders');

            // Estado del pedido
            $table->foreignId('status_id')
                  ->constrained('status_orders');

            /*
            |--------------------------------------------------------------------------
            | DATOS DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $table->string('payment_method')->nullable();

            $table->integer('total_items')->default(0);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->text('indication')->nullable();
            $table->text('comment')->nullable();

            $table->timestamp('ordered_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONTROL
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index('customer_id');
            $table->index('status_id');
            $table->index('delivery_user_id');
            $table->index('ordered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
