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
            // --- KATEGORI SMP Putra ---
            ['name' => 'Kemeja Putih SMP Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Putih SMP Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Putih SMP Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Biru Lengan Panjang SMP Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Biru Lengan Panjang SMP Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Biru Lengan Panjang SMP Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Batik SMP Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Batik SMP Putra (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Batik SMP Putra (XXXL)', 'price' => 230000],

            ['name' => 'Kemeja Biru List Bordir SMP Putra (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Biru List Bordir SMP Putra (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Biru List Bordir SMP Putra (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka SMP Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Pramuka SMP Putra (XL,XXL)', 'price' => 230000],
            ['name' => 'Kemeja Pramuka SMP Putra (XXXL)', 'price' => 240000],

            ['name' => 'Baju Olahraga SMP Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga SMP Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga SMP Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Olahraga SMP Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Celana Olahraga SMP Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Celana Olahraga SMP Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Biru SMP Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Biru SMP Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Biru SMP Putra (XXXL)', 'price' => 200000],

            ['name' => 'Celana Biru Gelap SMP Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Biru Gelap SMP Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Biru Gelap SMP Putra (XXXL)', 'price' => 200000],

            ['name' => 'Celana Pramuka SMP Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Pramuka SMP Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Pramuka SMP Putra (XXXL)', 'price' => 200000],

            // --- KATEGORI SMA Putra ---

            ['name' => 'Kemeja Putih SMA Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Putih SMA Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Putih SMA Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Abu Lengan Panjang SMA Putra (S,M,L)', 'price' => 210000],
            ['name' => 'Kemeja Abu Lengan Panjang SMA Putra (XL,XXL)', 'price' => 215000],
            ['name' => 'Kemeja Abu Lengan Panjang SMA Putra (XXXL)', 'price' => 225000],

            ['name' => 'Kemeja Batik SMA Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Batik SMA Putra (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Batik SMA Putra (XXXL)', 'price' => 230000],

            ['name' => 'Kemeja Cream List Bordir SMA Putra (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Cream List Bordir SMA Putra (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Cream List Bordir SMA Putra (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka SMA Putra (S,M,L)', 'price' => 225000],
            ['name' => 'Kemeja Pramuka SMA Putra (XL,XXL)', 'price' => 230000],
            ['name' => 'Kemeja Pramuka SMA Putra (XXXL)', 'price' => 240000],

            ['name' => 'Baju Olahraga SMA Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga SMA Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga SMA Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Olahraga SMA Putra (S,M,L)', 'price' => 150000],
            ['name' => 'Celana Olahraga SMA Putra (XL,XXL)', 'price' => 155000],
            ['name' => 'Celana Olahraga SMA Putra (XXXL)', 'price' => 160000],

            ['name' => 'Celana Abu SMA Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Abu SMA Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Abu SMA Putra (XXXL)', 'price' => 200000],

            ['name' => 'Celana Abu Tua SMA Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Abu Tua SMA Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Abu Tua SMA Putra (XXXL)', 'price' => 200000],

            ['name' => 'Celana Cream Coklat SMA Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Cream Coklat SMA Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Cream Coklat SMA Putra (XXXL)', 'price' => 200000],

            ['name' => 'Celana Pramuka SMA Putra (S,M,L)', 'price' => 185000],
            ['name' => 'Celana Pramuka SMA Putra (XL,XXL)', 'price' => 190000],
            ['name' => 'Celana Pramuka SMA Putra (XXXL)', 'price' => 200000],


            // --- KATEGORI SMP Putri ---
            ['name' => 'Jilbab Putih SMP Putri (S,M,L/XL,XXL)', 'price' => 120000],
            ['name' => 'Jilbab Biru SMP Putri (S,M,L/XL,XXL)', 'price' => 120000],
            ['name' => 'Jilbab Pramuka SMP Putri (S,M,L/XL,XXL)', 'price' => 120000],

            ['name' => 'Kemeja Putih SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Putih SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Putih SMP Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Biru SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Biru SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Biru SMP Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Batik SMP Putri (S,M,L)', 'price' => 230000],
            ['name' => 'Kemeja Batik SMP Putri (XL,XXL)', 'price' => 235000],
            ['name' => 'Kemeja Batik SMP Putri (XXXL)', 'price' => 245000],

            ['name' => 'Kemeja Biru List Bordir SMP Putri (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Biru List Bordir SMP Putri (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Biru List Bordir SMP Putri (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka SMP Putri (S,M,L)', 'price' => 240000],
            ['name' => 'Kemeja Pramuka SMP Putri (XL,XXL)', 'price' => 245000],
            ['name' => 'Kemeja Pramuka SMP Putri (XXXL)', 'price' => 255000],

            ['name' => 'Baju Olahraga SMP Putri (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga SMP Putri (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga SMP Putri (XXXL)', 'price' => 165000],

            ['name' => 'Rok Biru SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Biru SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Biru SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Biru Gelap SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Biru Gelap SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Biru Gelap SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Biru Stip Batik SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Biru Stip Batik SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Biru Stip Batik SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Biru Stip Biru SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Biru Stip Biru SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Biru Stip Biru SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Pramuka SMP Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Pramuka SMP Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Pramuka SMP Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Celana Pramuka SMP Putri (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Pramuka SMP Putri (XL,XXL)', 'price' => 210000],
            ['name' => 'Rok Celana Pramuka SMP Putri (XXXL)', 'price' => 225000],

            ['name' => 'Rok Celana Olahraga SMP Putri (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Olahraga SMP Putri (XL,XXL)', 'price' => 205000],
            ['name' => 'Rok Celana Olahraga SMP Putri (XXXL)', 'price' => 215000],

            // --- KATEGORI SMA Putri ---
            ['name' => 'Jilbab Putih SMA Putri (S,M,L/XL,XXL)', 'price' => 120000],
            ['name' => 'Jilbab Abu SMA Putri (S,M,L/XL,XXL)', 'price' => 120000],
            ['name' => 'Jilbab Cream SMA Putri (S,M,L/XL,XXL)', 'price' => 120000],
            ['name' => 'Jilbab Pramuka SMA Putri (S,M,L/XL,XXL)', 'price' => 120000],

            ['name' => 'Kemeja Putih SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Putih SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Putih SMA Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Biru SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Kemeja Biru SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Kemeja Biru SMA Putri (XXXL)', 'price' => 235000],

            ['name' => 'Kemeja Batik SMA Putri (S,M,L)', 'price' => 230000],
            ['name' => 'Kemeja Batik SMA Putri (XL,XXL)', 'price' => 235000],
            ['name' => 'Kemeja Batik SMA Putri (XXXL)', 'price' => 245000],

            ['name' => 'Kemeja Biru List Bordir SMA Putri (S,M,L)', 'price' => 235000],
            ['name' => 'Kemeja Biru List Bordir SMA Putri (XL,XXL)', 'price' => 240000],
            ['name' => 'Kemeja Biru List Bordir SMA Putri (XXXL)', 'price' => 250000],

            ['name' => 'Kemeja Pramuka SMA Putri (S,M,L)', 'price' => 240000],
            ['name' => 'Kemeja Pramuka SMA Putri (XL,XXL)', 'price' => 245000],
            ['name' => 'Kemeja Pramuka SMA Putri (XXXL)', 'price' => 255000],

            ['name' => 'Baju Olahraga SMA Putri (S,M,L)', 'price' => 150000],
            ['name' => 'Baju Olahraga SMA Putri (XL,XXL)', 'price' => 155000],
            ['name' => 'Baju Olahraga SMA Putri (XXXL)', 'price' => 165000],

            ['name' => 'Rok Abu SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Abu SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Abu SMA Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Abu Tua SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Abu Tua SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Abu Tua SMA Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Abu Tua Stip Batik SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Abu Tua Stip Batik SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Abu Tua Stip Batik SMA Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Abu Tua Stip Abu SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Abu Tua Stip Abu SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Abu Tua Stip Abu SMA Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Pramuka SMA Putri (S,M,L)', 'price' => 220000],
            ['name' => 'Rok Pramuka SMA Putri (XL,XXL)', 'price' => 225000],
            ['name' => 'Rok Pramuka SMA Putri (XXXL)', 'price' => 230000],

            ['name' => 'Rok Celana Pramuka SMA Putri (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Pramuka SMA Putri (XL,XXL)', 'price' => 210000],
            ['name' => 'Rok Celana Pramuka SMA Putri (XXXL)', 'price' => 225000],

            ['name' => 'Rok Celana Olahraga SMA Putri (S,M,L)', 'price' => 200000],
            ['name' => 'Rok Celana Olahraga SMA Putri (XL,XXL)', 'price' => 205000],
            ['name' => 'Rok Celana Olahraga SMA Putri (XXXL)', 'price' => 215000],

            // --- AKSESORI ---
            ['name' => 'Topi SMP', 'price' => 50000],
            ['name' => 'Dasi SMP', 'price' => 25000],
            ['name' => 'Topi SMA', 'price' => 50000],
            ['name' => 'Dasi SMA', 'price' => 25000],
        ];
    

        $catSmpPutra = \App\Models\ProductCategory::where('slug', 'seragam-smp-putra')->first()->id;
        $catSmpPutri = \App\Models\ProductCategory::where('slug', 'seragam-smp-putri')->first()->id;
        $catSmaPutra = \App\Models\ProductCategory::where('slug', 'seragam-sma-putra')->first()->id;
        $catSmaPutri = \App\Models\ProductCategory::where('slug', 'seragam-sma-putri')->first()->id;
        $catPerlengkapanSeragam = \App\Models\ProductCategory::where('slug', 'perlengkapan-seragam')->first()->id;

        foreach ($products as $item) {
            $name = $item['name'];

            // 2. Logika Penentuan Kategori Otomatis
            if (\Illuminate\Support\Str::contains($name, ['SMP Putra'])) {
                $categoryId = $catSmpPutra;
            } elseif (\Illuminate\Support\Str::contains($name, ['SMP Putra'])) {
                $categoryId = $catSmpPutri;
            } elseif (\Illuminate\Support\Str::contains($name, ['SMA Putra'])) {
                $categoryId = $catSmaPutra;
            } elseif (\Illuminate\Support\Str::contains($name, ['SMA Putri'])) {
                $categoryId = $catSmaPutri;
            } elseif (\Illuminate\Support\Str::contains($name, ['Topi', 'Dasi'])) {
                $categoryId = $catPerlengkapanSeragam;
            }

            // 3. Simpan ke Database
            \App\Models\Product::create([
                'name'                => $name,
                'slug'                => $this->slugName($name),
                'description'         => 'Keterangan ' . $name,
                'cost_price'          => $item['price'] * 0.7,
                'selling_price'       => $item['price'],
                'stock'               => 5,
                'is_active'           => true,
                'product_category_id' => $categoryId,
            ]);
        }
    }

    private function slugName($name)
    {
        $slugBase = \Illuminate\Support\Str::slug($name);

        // Coba buat slug unik
        do {
            // Generate 5 digit acak angka dan huruf kecil
            $randomCode = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
            $finalSlug = $slugBase . '-' . $randomCode;

            // Cek apakah slug ini sudah ada di tabel products
            $exists = \App\Models\Product::where('slug', $finalSlug)->exists();
        } while ($exists); // Jika ada yang sama, ulangi generate

        return $finalSlug;
    }
}
