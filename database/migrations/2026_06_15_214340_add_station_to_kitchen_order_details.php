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
        Schema::table('kitchen_order_details', function (Blueprint $table) {

            $table->foreignId('kitchen_station_id')
                ->nullable()
                ->after('order_detail_id')
                ->constrained('kitchen_stations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_order_details', function (Blueprint $table) {
            //
        });
    }
};
