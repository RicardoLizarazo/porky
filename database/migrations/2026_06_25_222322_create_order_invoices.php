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
        Schema::create('order_invoices', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTO
            |--------------------------------------------------------------------------
            */

            $table->string('document_type',20)->nullable();
            $table->string('document_number',50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | CLIENTE
            |--------------------------------------------------------------------------
            */

            $table->string('customer_name')->nullable();

            $table->string('company_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */

            $table->string('email')->nullable();

            $table->string('phone',30)->nullable();

            /*
            |--------------------------------------------------------------------------
            | UBICACIÓN
            |--------------------------------------------------------------------------
            */

            $table->string('address')->nullable();

            $table->string('city')->nullable();

            /*
            |--------------------------------------------------------------------------
            | DATOS TRIBUTARIOS (FUTURO)
            |--------------------------------------------------------------------------
            */

            $table->string('tax_regime')->nullable();

            $table->string('tax_responsibility')->nullable();

            /*
            |--------------------------------------------------------------------------
            | ESTADO DE FACTURACIÓN
            |--------------------------------------------------------------------------
            */

            // Ya fue enviada al software contable
            $table->boolean('is_generated')
                ->default(false);

            // Número asignado por el software contable
            $table->string('invoice_number')
                ->nullable();

            // Fecha de generación
            $table->timestamp('generated_at')
                ->nullable();

            // Observaciones
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index('document_number');
            $table->index('customer_name');
            $table->index('is_generated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_invoices');
    }
};