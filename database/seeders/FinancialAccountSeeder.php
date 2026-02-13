<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\FinancialAccount::create([
            'name' => 'Cash',
            'account_number' => null,
            'balance' => 0,
            'is_active' => true,
        ]);
        \App\Models\FinancialAccount::create([
            'name' => 'Bank BSI Koperasi NFBS Bogor',
            'account_number' => '1234567890',
            'balance' => 0,
            'is_active' => true,
        ]);
        \App\Models\FinancialAccount::create([
            'name' => 'Bank BJBS Koperasi NFBS Bogor',
            'account_number' => '12345674354',
            'balance' => 0,
            'is_active' => true,
        ]);
    }
}
