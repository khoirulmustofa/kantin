<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Supplier::create([
                'name' => 'Supplier ' . $i,
                'email' => 'supplier' . $i . '@example.com',
                'phone' => '08123456789' . $i,
                'contact_person' => 'Contact Person ' . $i,
                'address' => 'Address ' . $i,
                'city' => 'City ' . $i,
                'province' => 'Province ' . $i,
                'postal_code' => '12345' . $i,
            ]);
        }
    }
}
