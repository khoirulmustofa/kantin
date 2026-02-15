<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            SettingSeeder::class,

            ProductCategorySeeder::class,
            ProductSeeder::class,
            FinancialAccountSeeder::class,
            FinancialCategorySeeder::class,

            // FinancialMutationSeeder::class,
            // OrderSeeder::class,
            // SupplierSeeder::class,
            // PurchaseOrderSeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,

        ]);
    }
}
