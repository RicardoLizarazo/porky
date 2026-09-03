<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kitchen_order_details', function (Blueprint $table) {
            // Quién marcó el ticket como listo, o quién lo canceló
            // (reutiliza la misma columna para ambos casos).
            $table->foreignId('resolved_by')
                ->nullable()
                ->after('ready_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kitchen_order_details', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn('resolved_by');
        });
    }
};