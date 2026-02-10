<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinancialMutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = \App\Models\FinancialAccount::get();
        $categories = \App\Models\FinancialCategory::get();
        
        for($i = 0; $i < 60; $i++) {
            $account = $accounts->random();
            $category = $categories->random();
            \App\Models\FinancialMutation::create([
                'financial_account_id' => $account->id,
                'financial_category_id' => $category->id,
                'flow' => $category->type == 'income' ? 'in' : 'out',
                'amount' => rand(10000, 100000),
                'transaction_date' => Carbon::now()->subDays(rand(0, 30)),
                'description' => 'Financial Mutation ' . $i,
            ]);
        }
    }
}
