<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const menuActive = defineModel('menuActive', { type: String });

// Menu items based on the Sakai dashboard example
const items = ref([
    {
        items: [
            {
                label: 'Dashboard',
                icon: 'pi pi-home',
                route: 'admin.dashboard.index',
                menu: 'dashboard'
            }
        ]
    },
    {
        label: 'Master Data',
        items: [
            {
                label: 'User',
                icon: 'pi pi-users',
                route: 'admin.users.index',
                menu: 'users'
            },

        ]
    },
    {
        label: 'Koperasi',
        items: [
            {
                label: 'Supplier',
                icon: 'pi pi-truck',
                route: 'admin.suppliers.index',
                menu: 'suppliers'
            },
            {
                label: 'Category',
                icon: 'pi pi-list',
                route: 'admin.product-categories.index',
                menu: 'product-categories'
            },
            {
                label: 'Product',
                icon: 'pi pi-tag',
                route: 'admin.products.index',
                menu: 'products'
            },
            {
                label: 'Order',
                icon: 'pi pi-shopping-bag',
                route: 'admin.orders.index',
                menu: 'orders'
            },
            {
                label: 'Purchase Order',
                icon: 'pi pi-file-import',
                route: 'admin.purchase-orders.index',
                menu: 'purchase-orders'
            },
        ]
    },

    {
        label: 'Cash Flow',
        items: [
            {
                label: 'Account',
                icon: 'pi pi-wallet',
                route: 'admin.financial_accounts.index',
                menu: 'financial-accounts'
            },
            {
                label: 'Category',
                icon: 'pi pi-tags',
                route: 'admin.financial_categories.index',
                menu: 'financial-categories'
            },
            {
                label: 'Cash Inflow',
                icon: 'pi pi-plus-circle',
                route: 'admin.financial_cash_in.index',
                menu: 'financial-cash-in'
            },
            {
                label: 'Cash Outflow',
                icon: 'pi pi-minus-circle',
                route: 'admin.financial_cash_out.index',
                menu: 'financial-cash-out'
            },
            {
                label: 'Transaction History',
                icon: 'pi pi-history',
                route: 'admin.financial_transactions.index',
                menu: 'financial-transactions'
            },
        ]
    },
    {
        label: 'System Settings',
        items: [
            {
                label: 'Roles',
                icon: 'pi pi-shield',
                route: 'admin.roles.index',
                menu: 'roles'
            },
            {
                label: 'Settings',
                icon: 'pi pi-cog',
                route: 'admin.settings.index',
                menu: 'settings'
            },
            {
                label: 'Utilities',
                icon: 'pi pi-wrench',
                route: 'admin.utilities.index',
                menu: 'utilities'
            }


        ]
    }
]);

</script>

<template>
    <div
        class="flex flex-col h-full bg-white dark:bg-[#1e293b] text-gray-700 dark:text-gray-100 transition-colors duration-300">
        <!-- Logo Area -->
        <div class="flex items-center px-8 border-b border-gray-50 dark:border-gray-800 h-16 min-h-[4rem]">
            <Link :href="route('home')" class="flex items-center gap-3 group">
                <img :src="`/storage/${$page.props.settings.site_logo}`" alt="Logo" class=" w-auto"></img>
            </Link>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
            <ul class="flex flex-col gap-6">
                <template v-for="(category, i) in items" :key="i">
                    <li class="flex flex-col gap-1">
                        <!-- Category Label -->
                        <div v-if="category.label"
                            class="mb-2 px-3 text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
                            {{ category.label }}
                        </div>

                        <!-- Menu Items Group -->
                        <div class="flex flex-col gap-1">
                    <li v-for="(item, j) in category.items" :key="j">
                        <!-- Items with Route (Inertia) -->
                        <Link v-if="item.route" :href="route(item.route)"
                            class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 group"
                            :class="menuActive === item.menu ? 'text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 shadow-sm shadow-emerald-500/5' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white'">
                            <i :class="[item.icon, 'mr-3 text-lg transition-transform group-hover:scale-110']"></i>
                            <span>{{ item.label }}</span>
                            <i v-if="menuActive === item.menu"
                                class="pi pi-chevron-right ml-auto text-[10px] opacity-50"></i>
                        </Link>

                        <!-- Static Links -->
                        <Link v-else :href="item.to || '#'"
                            class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 group text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-white">
                            <i :class="[item.icon, 'mr-3 text-lg transition-transform group-hover:scale-110']"></i>
                            <span>{{ item.label }}</span>
                        </Link>
                    </li>
        </div>
        </li>
</template>
</ul>
</div>
</div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
</style>
