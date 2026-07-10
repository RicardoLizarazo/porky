<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_payments', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cash_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PAGO
            |--------------------------------------------------------------------------
            */

            $table->string('payment_method', 20);

            $table->decimal(
                'amount',
                12,
                2
            );

            $table->string('reference')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index('order_id');

            $table->index('cash_session_id');

            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'cash_payments'
        );
    }
};