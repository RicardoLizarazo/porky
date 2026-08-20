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
        Schema::create('order_cancellations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Pedido cancelado
            |--------------------------------------------------------------------------
            */

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Usuario que cancela
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Motivo
            |--------------------------------------------------------------------------
            */

            $table->string('reason', 100);


            /*
            |--------------------------------------------------------------------------
            | Observaciones adicionales
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();


            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_cancellations');
    }
};