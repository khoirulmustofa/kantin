<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ProductCategory::create([
            'name' => 'Makanan',
            'slug' => 'makanan',
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
        ]);
    }
}
