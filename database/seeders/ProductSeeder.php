<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\ProductCategory::get();
        
        for($i = 0; $i < 50; $i++) {
            $category = $categories->random();
            \App\Models\Product::create([
                'name' => 'Product ' . $i,
                'slug' => 'product-' . $i,
                'description' => 'Description Product ' . $i,
                'product_category_id' => $category->id,
                'price' => rand(10000, 100000),
                'stock' => rand(1, 10),
                'is_active' => true,
            ]);
        }
    }
}
