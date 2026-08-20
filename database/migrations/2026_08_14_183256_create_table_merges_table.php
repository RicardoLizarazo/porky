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
        Schema::create('table_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_table_id')->constrained('dining_tables');
            $table->foreignId('secondary_table_id')->constrained('dining_tables');
            $table->foreignId('primary_order_id')->constrained('orders');       // orden que sobrevive
            $table->foreignId('absorbed_order_id')->nullable()->constrained('orders'); // solo si la secundaria ya tenía orden (Caso B)
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('closed_at')->nullable(); // null = unión activa
            $table->timestamps();

            $table->index(['secondary_table_id', 'closed_at']);
            $table->index(['primary_table_id', 'closed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_merges');
    }
};
