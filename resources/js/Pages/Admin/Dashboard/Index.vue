<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Chart from 'primevue/chart';

import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    menu: String,
    title: String,
    stats: Object,
    chartData: Object,
    recent_products: Array,
});

const chartOptions = ref({
    maintainAspectRatio: false,
    aspectRatio: 0.8,
    plugins: {
        legend: {
            labels: {
                color: '#94a3b8',
                usePointStyle: true,
                font: {
                    weight: 'bold',
                    size: 11
                }
            }
        },
        tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#1e293b',
            bodyColor: '#475569',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
        }
    },
    scales: {
        x: {
            ticks: {
                color: '#94a3b8',
                font: {
                    size: 10,
                    weight: 'bold'
                }
            },
            grid: {
                display: false,
            }
        },
        y: {
            ticks: {
                color: '#94a3b8',
                callback: function (value) {
                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                    return value;
                },
                font: {
                    size: 10,
                    weight: 'bold'
                }
            },
            grid: {
                color: 'rgba(148, 163, 184, 0.1)',
            }
        }
    }
});
</script>

<template>

    <Head :title="props.title" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <!-- Stats Recap Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-2">
            <!-- Total In Card -->
            <div
                class="relative overflow-hidden group p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:rotate-12 transition-transform duration-500">
                    <i class="pi pi-arrow-down-left text-7xl text-emerald-600"></i>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-inner">
                        <i class="pi pi-arrow-down-left text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Income</span>
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">
                        {{ formatCurrencyIndo(stats?.total_in || 0) }}
                    </h2>
                    <p
                        class="text-[10px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-full w-fit">
                        Lifetime Revenue
                    </p>
                </div>
            </div>

            <!-- Total Out Card -->
            <div
                class="relative overflow-hidden group p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:rotate-12 transition-transform duration-500">
                    <i class="pi pi-arrow-up-right text-7xl text-rose-600"></i>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-inner">
                        <i class="pi pi-arrow-up-right text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Expense</span>
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">
                        {{ formatCurrencyIndo(stats?.total_out || 0) }}
                    </h2>
                    <p
                        class="text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-500/10 px-2 py-0.5 rounded-full w-fit">
                        Lifetime Spending
                    </p>
                </div>
            </div>

            <!-- Net Balance Card -->
            <div
                class="relative overflow-hidden group p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:rotate-12 transition-transform duration-500">
                    <i class="pi pi-wallet text-7xl text-blue-600"></i>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
                        <i class="pi pi-wallet text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Net Balance</span>
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">
                        {{ formatCurrencyIndo(stats?.total_balance || 0) }}
                    </h2>
                    <p
                        class="text-[10px] font-bold text-blue-500 bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded-full w-fit">
                        Available Resources
                    </p>
                </div>
            </div>

            <div
                class="relative overflow-hidden group p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:rotate-12 transition-transform duration-500">
                    <i class="pi pi-shopping-cart text-7xl text-blue-600"></i>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
                        <i class="pi pi-shopping-cart text-xl"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Orders</span>
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter mb-1">
                        {{ stats?.total_orders || 0 }}
                    </h2>
                    <p
                        class="text-[10px] font-bold text-blue-500 bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded-full w-fit">
                        Total Orders
                    </p>
                </div>
            </div>
        </div>

        <!-- Financial Chart Section -->
        <div
            class="card p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm mb-8">
            <div class="flex flex-col mb-6">
                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Financial Performance</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue vs Expenses (Last 30
                    Days)</p>
            </div>
            <div class="h-[30rem]">
                <Chart type="bar" :data="props.chartData" :options="chartOptions" class="h-full" />
            </div>
        </div>

        <div
            class="card p-4 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all duration-300">
            <div class="flex items-center justify-between mb-6 px-2">
                <div class="flex flex-col">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Recent Products</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Latest Inventory Additions
                    </p>
                </div>
                <Link :href="route('admin.products.index')">
                    <Button label="View All" icon="pi pi-external-link" text severity="secondary" size="small"
                        class="font-black text-[10px] uppercase tracking-widest" />
                </Link>
            </div>

            <DataTable :value="recent_products" scrollable tableStyle="min-width: 50rem" class="p-datatable-sm"
                stripedRows>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <i class="pi pi-box text-4xl mb-2 opacity-20"></i>
                        <p class="text-xs font-bold uppercase tracking-wider">No products found</p>
                    </div>
                </template>
                <Column field="name" header="Name" style="min-width: 15rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white">{{ slotProps.data.name }}</span>
                            <span class="text-[10px] text-gray-400 font-mono uppercase">{{ slotProps.data.slug }}</span>
                        </div>
                    </template>
                </Column>
                <Column field="category.name" header="Category" style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.category?.name || 'Uncategorized'" severity="info" rounded
                            class="text-[9px] uppercase font-black" />
                    </template>
                </Column>
                <Column field="price" header="Price" style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="font-black text-gray-700 dark:text-gray-300">{{
                            formatCurrencyIndo(slotProps.data.price) }}</span>
                    </template>
                </Column>
                <Column field="stock" header="In Stock" style="min-width: 10rem">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <span :class="[
                                'w-2 h-2 rounded-full',
                                slotProps.data.stock > 10 ? 'bg-emerald-500' : slotProps.data.stock > 0 ? 'bg-amber-500' : 'bg-rose-500'
                            ]"></span>
                            <span class="font-bold">{{ slotProps.data.stock }}</span>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>