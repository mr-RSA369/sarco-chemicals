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
        if (!Schema::hasTable('inventory_batches')) {
            Schema::create('inventory_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->string('batch_no')->nullable();
                $table->date('mfg_date')->nullable();
                $table->date('exp_date')->nullable();
                $table->string('rack_no')->nullable();
                $table->integer('qty_per_bundle')->default(1);
                $table->decimal('current_qty', 15, 2)->default(0);

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
        Schema::dropIfExists('inventory_batches');
    }
};
