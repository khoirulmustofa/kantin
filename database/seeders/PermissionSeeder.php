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

        Permission::create([
            'name' => 'Dashboard',
            'group' => 'Dashboard',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'User List',
            'group' => 'User',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'User Create',
            'group' => 'User',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'User Edit',
            'group' => 'User',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'User Delete',
            'group' => 'User',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Role List',
            'group' => 'Role',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Role Create',
            'group' => 'Role',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Role Edit',
            'group' => 'Role',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Role Delete',
            'group' => 'Role',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Permission List',
            'group' => 'Permission',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Permission Create',
            'group' => 'Permission',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Permission Edit',
            'group' => 'Permission',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Permission Delete',
            'group' => 'Permission',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Supplier List',
            'group' => 'Supplier',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Supplier Create',
            'group' => 'Supplier',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Supplier Edit',
            'group' => 'Supplier',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Supplier Delete',
            'group' => 'Supplier',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Product List',
            'group' => 'Product',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Create',
            'group' => 'Product',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Edit',
            'group' => 'Product',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Delete',
            'group' => 'Product',
            'guard_name' => 'web',
        ]);


        Permission::create([
            'name' => 'Product Category List',
            'group' => 'Product Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Category Create',
            'group' => 'Product Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Category Edit',
            'group' => 'Product Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Product Category Delete',
            'group' => 'Product Category',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Purchase List',
            'group' => 'Purchase',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Purchase Create',
            'group' => 'Purchase',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Purchase Edit',
            'group' => 'Purchase',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Purchase Delete',
            'group' => 'Purchase',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Order List',
            'group' => 'Order',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Order Create',
            'group' => 'Order',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Order Edit',
            'group' => 'Order',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Order Delete',
            'group' => 'Order',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Financial Account List',
            'group' => 'Financial Account',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Account Create',
            'group' => 'Financial Account',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Account Edit',
            'group' => 'Financial Account',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Account Delete',
            'group' => 'Financial Account',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Financial Category List',
            'group' => 'Financial Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Category Create',
            'group' => 'Financial Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Category Edit',
            'group' => 'Financial Category',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Financial Category Delete',
            'group' => 'Financial Category',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Cash Inflow List',
            'group' => 'Cash Inflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Inflow Create',
            'group' => 'Cash Inflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Inflow Edit',
            'group' => 'Cash Inflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Inflow Delete',
            'group' => 'Cash Inflow',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Cash Outflow List',
            'group' => 'Cash Outflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Outflow Create',
            'group' => 'Cash Outflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Outflow Edit',
            'group' => 'Cash Outflow',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'Cash Outflow Delete',
            'group' => 'Cash Outflow',
            'guard_name' => 'web',
        ]);

        Permission::create([
            'name' => 'Bank Transaction List',
            'group' => 'Bank Transaction',
            'guard_name' => 'web',
        ]);
        
      
    }
}
