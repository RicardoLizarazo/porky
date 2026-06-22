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
        Schema::create('kitchen_order_details', function (Blueprint $table) {

            $table->id();

            $table->foreignId('kitchen_order_id');

            $table->foreignId('order_detail_id');

            $table->string('product_name');

            $table->integer('quantity');

            $table->text('comment')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_order_details');
    }
};
