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
        if (!Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table) {
                $table->id();

                $table->foreignId('item_id')
                    ->constrained();
                $table->foreignId('inventory_batch_id')
                    ->nullable()
                    ->constrained();
                $table->date('transaction_date');
                $table->enum('transaction_type', ['receipt', 'issue']);
                $table->decimal('quantity', 15, 2);
                $table->text('remarks')
                    ->nullable();
                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users');

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
