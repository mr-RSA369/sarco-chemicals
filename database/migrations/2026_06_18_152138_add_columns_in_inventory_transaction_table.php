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
                if (!Schema::hasColumns('inventory_transactions', ['item_bundle_size_id', 'bundle_count'])) {
                    $table->foreignId('item_bundle_size_id')
                        ->nullable()
                        ->constrained();
                    $table->integer('bundle_count')
                        ->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (Schema::hasColumns('inventory_transactions', ['item_bundle_size_id', 'bundle_count'])) {
                $table->dropForeign(['item_bundle_size_id']);
                $table->dropColumn(['item_bundle_size_id', 'bundle_count']);
            }
        });
    }
};
