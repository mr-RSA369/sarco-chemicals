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

        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumns('inventory_transactions', ['opening_balance', 'closing_balance'])) {
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('closing_balance', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (Schema::hasColumns('inventory_transactions', ['opening_balance', 'closing_balance'])) {
                $table->dropColumn(['opening_balance', 'closing_balance']);
            }
        });
    }
};
