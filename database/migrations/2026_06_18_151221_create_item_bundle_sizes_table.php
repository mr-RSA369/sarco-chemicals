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
        if (!Schema::hasTable('item_bundle_sizes')) {
            Schema::create('item_bundle_sizes', function (Blueprint $table) {
                $table->id();

                $table->foreignId('item_id')
                    ->constrained()
                    ->cascadeOnDelete();
                $table->integer('bundle_size');
                $table->integer('available_bundles')
                    ->default(0);

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['item_id', 'bundle_size']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_bundle_sizes');
    }
};
