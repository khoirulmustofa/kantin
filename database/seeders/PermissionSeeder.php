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

        Permission::updateOrCreate(
            [
                'name' => 'Dashboard',
                'group' => 'Dashboard',
            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'User List',
                'group' => 'User',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'User Create',
                'group' => 'User',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'User Edit',
                'group' => 'User',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'User Delete',
                'group' => 'User',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Role List',
                'group' => 'Role',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Role Create',
                'group' => 'Role',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Role Edit',
                'group' => 'Role',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Role Delete',
                'group' => 'Role',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Permission List',
                'group' => 'Permission',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Permission Create',
                'group' => 'Permission',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Permission Edit',
                'group' => 'Permission',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Permission Delete',
                'group' => 'Permission',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Supplier List',
                'group' => 'Supplier',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Supplier Create',
                'group' => 'Supplier',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Supplier Edit',
                'group' => 'Supplier',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Supplier Delete',
                'group' => 'Supplier',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Product List',
                'group' => 'Product',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Create',
                'group' => 'Product',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Edit',
                'group' => 'Product',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Delete',
                'group' => 'Product',

            ],
            ['guard_name' => 'web',]
        );


        Permission::updateOrCreate(
            [
                'name' => 'Product Category List',
                'group' => 'Product Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Category Create',
                'group' => 'Product Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Category Edit',
                'group' => 'Product Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Product Category Delete',
                'group' => 'Product Category',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Purchase List',
                'group' => 'Purchase',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Purchase Create',
                'group' => 'Purchase',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Purchase Edit',
                'group' => 'Purchase',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Purchase Delete',
                'group' => 'Purchase',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Order List',
                'group' => 'Order',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Order Create',
                'group' => 'Order',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Order Edit',
                'group' => 'Order',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Order Delete',
                'group' => 'Order',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Financial Account List',
                'group' => 'Financial Account',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Account Create',
                'group' => 'Financial Account',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Account Edit',
                'group' => 'Financial Account',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Account Delete',
                'group' => 'Financial Account',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Financial Category List',
                'group' => 'Financial Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Category Create',
                'group' => 'Financial Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Category Edit',
                'group' => 'Financial Category',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Financial Category Delete',
                'group' => 'Financial Category',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Cash Inflow List',
                'group' => 'Cash Inflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Inflow Create',
                'group' => 'Cash Inflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Inflow Edit',
                'group' => 'Cash Inflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Inflow Delete',
                'group' => 'Cash Inflow',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Cash Outflow List',
                'group' => 'Cash Outflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Outflow Create',
                'group' => 'Cash Outflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Outflow Edit',
                'group' => 'Cash Outflow',

            ],
            ['guard_name' => 'web',]
        );
        Permission::updateOrCreate(
            [
                'name' => 'Cash Outflow Delete',
                'group' => 'Cash Outflow',

            ],
            ['guard_name' => 'web',]
        );

        Permission::updateOrCreate(
            [
                'name' => 'Bank Transaction List',
                'group' => 'Bank Transaction',

            ],
            ['guard_name' => 'web',]
        );
    }
}
