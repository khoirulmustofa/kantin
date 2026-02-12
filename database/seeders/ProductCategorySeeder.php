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
            'name' => 'Seragam SMP Putra',
            'slug' => 'seragam-smp-putra',
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Seragam SMP Putri',
            'slug' => 'seragam-smp-putri',
        ]);

        \App\Models\ProductCategory::create([
            'name' => 'Seragam SMA Putra',
            'slug' => 'seragam-sma-putra',
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Seragam SMA Putri',
            'slug' => 'seragam-sma-putri',
        ]);

        \App\Models\ProductCategory::create([
            'name' => 'Perlengkapan ATK',
            'slug' => 'perlengkapan-atk',
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Perlengkapan Seragam',
            'slug' => 'perlengkapan-seragam',
        ]);
    }
}
