<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\FinancialCategory::create([
            'name' => 'Hasil Penjualan',
            'type' => 'income',
        ]);

        \App\Models\FinancialCategory::create([
            'name' => 'Transfer Masuk',
            'type' => 'income',
        ]);
        \App\Models\FinancialCategory::create([
            'name' => 'Biaya Operasional',
            'type' => 'expense',
        ]);
        \App\Models\FinancialCategory::create([
            'name' => 'Gaji Pegawai',
            'type' => 'expense',
        ]);
        \App\Models\FinancialCategory::create([
            'name' => 'Pajak',
            'type' => 'expense',
        ]);
    }
}
