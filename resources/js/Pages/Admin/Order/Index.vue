<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import debounce from 'lodash/debounce';
import { formatCurrencyIndo, formatDateIndonesian } from '@/Utils/formatter';

const props = defineProps({
    menu: String,
    title: String,
    orders: Object,
    filters: Object,
});

const confirm = useConfirm();

const search = ref(props.filters.search || '');
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.orders.index'), {
        search: search.value,
        rows: extraParams.rows || props.orders.per_page,
        multiSortMeta: multiSortMeta.value.length ? JSON.stringify(multiSortMeta.value) : null,
        ...extraParams
    }, {
        preserveState: true,
        replace: true,
        onBefore: () => { loading.value = true },
        onFinish: () => { loading.value = false }
    });
};

watch(search, debounce((value) => {
    loadLazyData({ page: 1 });
}, 300));

const onPage = (event) => {
    loadLazyData({
        rows: event.rows,
        page: event.page + 1
    });
};

const onSort = (event) => {
    multiSortMeta.value = event.multiSortMeta;
    loadLazyData({ page: 1 });
};

const confirmDeleteOrder = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.order_number}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.orders.destroy', data.id), {
                onSuccess: () => {
                    loading.value = false;
                },
                onFinish: () => {
                    loading.value = false;
                }
            });
        }
    });
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'completed': return 'success';
        case 'processing': return 'info';
        case 'pending': return 'warn';
        case 'cancelled': return 'danger';
        default: return null;
    }
};

const getPaymentSeverity = (status) => {
    switch (status) {
        case 'paid': return 'success';
        case 'unpaid': return 'warn';
        case 'failed': return 'danger';
        default: return null;
    }
};

const syncFinance = (data) => {
    confirm.require({
        message: `Sync ${data.order_number} to financial ledger?`,
        header: 'Financial Sync',
        icon: 'pi pi-dollar',
        acceptLabel: 'Sync',
        rejectLabel: 'Cancel',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-success',
        accept: () => {
            loading.value = true;
            router.post(route('admin.orders.sync_finance', data.id), {}, {
                onSuccess: () => {
                    // Flash message handled by AdminLayout
                },
                onFinish: () => {
                    loading.value = false;
                }
            });
        }
    });
};
</script>

<template>

    <Head :title="props.title" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <Toolbar class="!border-0 !border-b !p-0 !pb-2 dark:!bg-gray-800">
                <template #start>
                    <div class="w-full sm:w-auto">
                        <Link :href="route('admin.orders.create')">
                            <Button label="New Order" icon="pi pi-plus" severity="primary" class="w-full sm:w-auto" />
                        </Link>
                    </div>
                </template>

                <template #end>
                    <div class="w-full flex justify-end">
                        <IconField class="w-full">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search orders..." class="w-full sm:w-64" />
                            <InputIcon v-if="search" @click="search = ''"
                                class="cursor-pointer hover:text-red-500 transition-colors"
                                style="right: 0.75rem; left: auto;">
                                <i class="pi pi-times" />
                            </InputIcon>
                        </IconField>
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="orders.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(orders.current_page - 1) * orders.per_page" :rows="orders.per_page"
                :totalRecords="orders.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 60rem">

                <template #empty> No orders found. </template>
                <template #loading> Loading orders... </template>

                <Column header="Actions" :exportable="false" style="min-width: 10rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-1">
                            <!-- new tab -->
                            <a :href="route('order.show', slotProps.data.id)" v-tooltip.top="'View Invoice'"
                                target="_blank">
                                <Button icon="pi pi-file" text rounded severity="secondary" />
                            </a>

                            <Button v-if="!slotProps.data.mutation && slotProps.data.payment_status === 'paid'"
                                icon="pi pi-sync" text rounded severity="success" v-tooltip.top="'Sync to Finance'"
                                @click="syncFinance(slotProps.data)" />
                            <i v-else-if="slotProps.data.mutation" class="pi pi-check-circle text-emerald-500 p-2"
                                v-tooltip.top="'Already Synced'" />

                            <Link :href="route('admin.orders.edit', slotProps.data.id)"
                                v-if="slotProps.data.status !== 'completed' && slotProps.data.payment_status !== 'paid'">
                            <Button icon="pi pi-pencil" text rounded severity="info" v-tooltip.top="'Edit Order'" />
                            </Link>
                            <Button icon="pi pi-trash" text rounded severity="danger" v-tooltip.top="'Delete Order'"
                                @click="confirmDeleteOrder(slotProps.data)"
                                v-if="slotProps.data.payment_status !== 'paid'" />
                        </div>
                    </template>
                </Column>
                <Column field="order_number" header="Order Code" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ slotProps.data.order_number
                            }}</span>
                    </template>
                </Column>

                <Column field="user.name" header="Customer" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white">{{ slotProps.data.user?.name }}</span>
                            <span class="text-[10px] text-gray-400">{{ slotProps.data.user?.email }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="grand_total" header="Total" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="font-bold">{{ formatCurrencyIndo(slotProps.data.grand_total) }}</span>
                    </template>
                </Column>

                <Column field="status" header="Status" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.status" :severity="getStatusSeverity(slotProps.data.status)" rounded
                            class="uppercase text-[10px]" />
                    </template>
                </Column>

                <Column field="payment_status" header="Payment" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.payment_status"
                            :severity="getPaymentSeverity(slotProps.data.payment_status)" rounded
                            class="uppercase text-[10px]" />
                    </template>
                </Column>

                <Column field="financial_account.name" header="Method" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ slotProps.data.financial_account?.name || '-' }}
                        </span>
                    </template>
                </Column>

                <Column field="created_at" header="Date" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="text-sm">{{ formatDateIndonesian(slotProps.data.created_at) }}</span>
                    </template>
                </Column>


            </DataTable>
        </div>
    </AdminLayout>
</template>
