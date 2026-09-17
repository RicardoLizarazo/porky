<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Las salidas automáticas por venta (guard "customer", autoservicio) no
     * tienen un usuario staff que anotar como responsable del movimiento.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE inventory_movements MODIFY created_by BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE inventory_movements MODIFY created_by BIGINT UNSIGNED NOT NULL');
    }
};
