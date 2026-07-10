<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {

            $table->text('opening_notes')
                ->nullable()
                ->after('opening_amount');

            $table->text('closing_notes')
                ->nullable()
                ->after('closing_amount');

            $table->foreignId('opened_by')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('closed_by')
                ->nullable()
                ->after('opened_by')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {

            $table->dropForeign(['opened_by']);
            $table->dropForeign(['closed_by']);

            $table->dropColumn([
                'opening_notes',
                'closing_notes',
                'opened_by',
                'closed_by',
            ]);
        });
    }
};