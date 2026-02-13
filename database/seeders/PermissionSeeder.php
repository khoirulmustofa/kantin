<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 1. Dashboard
        Permission::updateOrCreate(
            ['name' => 'Dashboard View', 'group' => 'Dashboard'],
            ['guard_name' => 'web']
        );

        // 2. User Management (Melengkapi yang sudah ada)
        Permission::updateOrCreate(
            ['name' => 'User Delete', 'group' => 'User'],
            ['guard_name' => 'web']
        );

        // 3. Product Categories
        Permission::updateOrCreate(
            ['name' => 'Product Category List', 'group' => 'Product Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Category Create', 'group' => 'Product Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Category Edit', 'group' => 'Product Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Category Delete', 'group' => 'Product Category'],
            ['guard_name' => 'web']
        );

        // 4. Products
        Permission::updateOrCreate(
            ['name' => 'Product List', 'group' => 'Product'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Create', 'group' => 'Product'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Edit', 'group' => 'Product'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Delete', 'group' => 'Product'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Product Duplicate', 'group' => 'Product'],
            ['guard_name' => 'web']
        );

        // 5. Orders (Penjualan)
        Permission::updateOrCreate(
            ['name' => 'Order List', 'group' => 'Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Order Create', 'group' => 'Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Order Edit', 'group' => 'Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Order Delete', 'group' => 'Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Order Sync Finance', 'group' => 'Order'], // Khusus untuk sync ke keuangan
            ['guard_name' => 'web']
        );

        // 6. Financial Accounts
        Permission::updateOrCreate(
            ['name' => 'Financial Account List', 'group' => 'Financial Account'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Account Create', 'group' => 'Financial Account'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Account Edit', 'group' => 'Financial Account'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Account Delete', 'group' => 'Financial Account'],
            ['guard_name' => 'web']
        );

        // 7. Financial Categories
        Permission::updateOrCreate(
            ['name' => 'Financial Category List', 'group' => 'Financial Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Category Create', 'group' => 'Financial Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Category Edit', 'group' => 'Financial Category'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Financial Category Delete', 'group' => 'Financial Category'],
            ['guard_name' => 'web']
        );

        // 8. Cash Inflow (Pemasukan)
        Permission::updateOrCreate(
            ['name' => 'Cash Inflow List', 'group' => 'Cash Inflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Inflow Create', 'group' => 'Cash Inflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Inflow Edit', 'group' => 'Cash Inflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Inflow Delete', 'group' => 'Cash Inflow'],
            ['guard_name' => 'web']
        );

        // 9. Cash Outflow (Pengeluaran)
        Permission::updateOrCreate(
            ['name' => 'Cash Outflow List', 'group' => 'Cash Outflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Outflow Create', 'group' => 'Cash Outflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Outflow Edit', 'group' => 'Cash Outflow'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Cash Outflow Delete', 'group' => 'Cash Outflow'],
            ['guard_name' => 'web']
        );

        // 10. Financial Transactions (History)
        Permission::updateOrCreate(
            ['name' => 'Financial Transaction List', 'group' => 'Financial Transaction'],
            ['guard_name' => 'web']
        );

        // 11. Suppliers
        Permission::updateOrCreate(
            ['name' => 'Supplier List', 'group' => 'Supplier'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Supplier Create', 'group' => 'Supplier'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Supplier Edit', 'group' => 'Supplier'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Supplier Delete', 'group' => 'Supplier'],
            ['guard_name' => 'web']
        );

        // 12. Purchase Orders (Pembelian ke Supplier)
        Permission::updateOrCreate(
            ['name' => 'Purchase Order List', 'group' => 'Purchase Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Purchase Order Create', 'group' => 'Purchase Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Purchase Order Edit', 'group' => 'Purchase Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Purchase Order Delete', 'group' => 'Purchase Order'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Purchase Order Sync Finance', 'group' => 'Purchase Order'],
            ['guard_name' => 'web']
        );

        // 13. Roles
        Permission::updateOrCreate(
            ['name' => 'Role List', 'group' => 'Role'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Role Create', 'group' => 'Role'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Role Edit', 'group' => 'Role'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Role Delete', 'group' => 'Role'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Role Assign Users', 'group' => 'Role'], // Untuk toggle user
            ['guard_name' => 'web']
        );

        // 14. Settings
        Permission::updateOrCreate(
            ['name' => 'Setting View', 'group' => 'Setting'],
            ['guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'Setting Edit', 'group' => 'Setting'], // Termasuk update & delete slider
            ['guard_name' => 'web']
        );

        // 15. Utilities
        Permission::updateOrCreate(
            ['name' => 'Utility Manage', 'group' => 'Utility'], // Clear cache, log, session, dll
            ['guard_name' => 'web']
        );
    }
}
