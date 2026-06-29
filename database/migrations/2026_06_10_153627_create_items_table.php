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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('item_categories');

            $table->foreignId('type_id')
                ->constrained('item_types');

            $table->string('product_name')->nullable();

            $table->string('item_name');

            $table->integer('size')->nullable();

            $table->enum('size_unit', [
                'ML',
                'GM',
                'KG',
                'LTR'
            ])->nullable();

            $table->integer('minimum_qty')
                ->default(0);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
