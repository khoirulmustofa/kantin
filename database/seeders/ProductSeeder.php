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

        $products = [
            // --- KATEGORI Putra ---
            ['name' => 'Kemeja Senin Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Senin Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Senin Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Selasa Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Selasa Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Selasa Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Batik Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Batik Putra (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Batik Putra (XXXL)', 'price' => 230000],

            ['name' => 'Kemeja Biru List Putra (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Biru List Putra (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Biru List Putra (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Pramuka Putra (XL,XXL)', 'price' => 230000],
            ['name' => 'Kemeja Pramuka Putra (XXXL)', 'price' => 240000],

            ['name' => 'Baju Olahraga Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Olahraga Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Celana Olahraga Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Celana Olahraga Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Seragam Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Seragam Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Seragam Putra (XXXL)', 'price' => 200000],

            // --- KATEGORI Putri ---
            ['name' => 'Jilbab Seragam Putri (S,M,L/XL,XXL)', 'price' => 120000],

            ['name' => 'Kemeja Senin Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Senin Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Senin Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Biru Polos Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Biru Polos Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Biru Polos Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Batik Putri (S,M,L)', 'price' => 230000],
            ['name' => 'Kemeja Batik Putri (XL,XXL)', 'price' => 235000],
            ['name' => 'Kemeja Batik Putri (XXXL)', 'price' => 245000],

            ['name' => 'Kemeja Biru List Putri (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Biru List Putri (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Biru List Putri (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka Putri (S,M,L)', 'price' => 240000],
            ['name' => 'Kemeja Pramuka Putri (XL,XXL)', 'price' => 245000],
            ['name' => 'Kemeja Pramuka Putri (XXXL)', 'price' => 255000],

            ['name' => 'Rok Seragam SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Seragam SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Seragam SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Celana Pramuka (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Pramuka (XL,XXL)', 'price' => 210000],
            ['name' => 'Rok Celana Pramuka (XXXL)', 'price' => 225000],

            ['name' => 'Rok Celana Olahraga (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Olahraga (XL,XXL)', 'price' => 205000],
            ['name' => 'Rok Celana Olahraga (XXXL)', 'price' => 215000],

            ['name' => 'Baju Olahraga Putri (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga Putri (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga Putri (XXXL)', 'price' => 165000],

            ['name' => 'Rok Seragam SMA (S,M,L)', 'price' => 225000],
            ['name' => 'Rok Seragam SMA (XL,XXL)', 'price' => 230000],
            ['name' => 'Rok Seragam SMA (XXXL)', 'price' => 235000],

            // --- AKSESORI ---
            ['name' => 'Topi', 'price' => 50000],
            ['name' => 'Dasi', 'price' => 25000],
        ];

        $catSmpPutra = \App\Models\ProductCategory::where('slug', 'seragam-smp-putra')->first()->id;
        $catSmpPutri = \App\Models\ProductCategory::where('slug', 'seragam-smp-putri')->first()->id;       
        $catPerlengkapanSeragam = \App\Models\ProductCategory::where('slug', 'perlengkapan-seragam')->first()->id;

        foreach ($products as $item) {
            $name = $item['name'];

            // 2. Logika Penentuan Kategori Otomatis
            if (\Illuminate\Support\Str::contains($name, ['Putra'])) {
                $categoryId = $catSmpPutra;
            } elseif (\Illuminate\Support\Str::contains($name, ['Putri','Rok','Jilbab'])) {
                $categoryId = $catSmpPutri;
            } elseif (\Illuminate\Support\Str::contains($name, ['Topi', 'Dasi'])) {
                $categoryId = $catPerlengkapanSeragam;
            }

            // 3. Simpan ke Database
            \App\Models\Product::create([
                'name'                => $name,
                'slug'                => \Illuminate\Support\Str::slug($name),
                'description'         => 'Keterangan ' . $name,
                'cost_price'          => 0,
                'selling_price'       => $item['price'],
                'stock'               => 5,
                'is_active'           => true,
                'product_category_id' => $categoryId,
            ]);
        }
    }
}
