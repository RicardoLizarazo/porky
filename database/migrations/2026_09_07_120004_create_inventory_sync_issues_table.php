<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_sync_issues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->string('reason');
            $table->unsignedInteger('occurrences')->default(1);

            $table->foreignId('last_order_detail_id')
                ->nullable()
                ->constrained('order_details')
                ->nullOnDelete();

            $table->timestamp('last_seen_at');

            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_sync_issues');
    }
};
