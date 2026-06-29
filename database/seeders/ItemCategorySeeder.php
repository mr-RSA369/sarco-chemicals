<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Bottles', 'Packing Materials', 'Chemicals'];

        foreach ($categories as $category) {
            DB::table('item_categories')->insert(['name' => $category]);
        }
    }
}
