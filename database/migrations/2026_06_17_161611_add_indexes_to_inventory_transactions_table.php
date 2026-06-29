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
        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->index('item_id');
                $table->index('inventory_batch_id');
                $table->index('transaction_date');
                $table->index([
                    'item_id',
                    'transaction_date'
                ], 'item_transaction_date_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropIndex('item_transaction_date_index');
                $table->dropIndex('transaction_date');
                $table->dropIndex('inventory_batch_id');
                $table->dropIndex('item_id');
            });
            }

    }
};
