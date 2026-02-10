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

        for ($i = 0; $i < 50; $i++) {
            $category = $categories->random();
            $cost_price = rand(10000, 100000);
            $selling_price = $cost_price * 1.3;

            \App\Models\Product::create([
                'name' => 'Product ' . $i,
                'slug' => 'product-' . $i,
                'description' => 'Description Product ' . $i,
                'product_category_id' => $category->id,
                'cost_price' => $cost_price,
                'selling_price' => $selling_price,
                'stock' => rand(1, 10),
                'is_active' => true,
            ]);
        }
    }
}
