<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kitchen_order_details', function (Blueprint $table) {
            $table->enum('status', ['pending', 'ready'])
                ->default('pending')
                ->after('comment');

            $table->timestamp('ready_at')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('kitchen_order_details', function (Blueprint $table) {
            $table->dropColumn(['status', 'ready_at']);
        });
    }
};