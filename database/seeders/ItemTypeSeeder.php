<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['category_id' => 1, 'name' => 'Bottles'],
            ['category_id' => 1, 'name' => 'Caps'],
            ['category_id' => 1, 'name' => 'Pin Droppers'],
            ['category_id' => 2, 'name' => 'Labels'],
            ['category_id' => 2, 'name' => 'Boxes'],
            ['category_id' => 2, 'name' => 'Cartons'],
            ['category_id' => 3, 'name' => 'Liquids'],
            ['category_id' => 3, 'name' => 'Powders'],
        ];

        foreach ($types as $type) {
            DB::table('item_types')->insert([
                'category_id' => $type['category_id'],
                'name' => $type['name'],
            ]);
        }
    }
}
