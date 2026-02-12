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
    purchaseOrders: Object,
    filters: Object,
});

const confirm = useConfirm();

const search = ref(props.filters.search || '');
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.purchase_orders.index'), {
        search: search.value,
        rows: extraParams.rows || props.purchaseOrders.per_page,
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

const confirmDeletePO = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.po_number}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.purchase_orders.destroy', data.id), {
                onSuccess: () => {
                    loading.value = false;
                },
                onError: () => {
                    loading.value = false;
                }
            });
        }
    });
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'received': return 'success';
        case 'ordered': return 'info';
        case 'draft': return 'warn';
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
        message: `Sync ${data.po_number} to financial ledger as expense?`,
        header: 'Financial Sync',
        icon: 'pi pi-dollar',
        rejectLabel: 'Cancel',
        acceptLabel: 'Sync',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-success',
        accept: () => {
            loading.value = true;
            router.post(route('admin.purchase_orders.sync_finance', data.id), {}, {
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
            class="card p-2 sm:p-4 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
            <Toolbar class="!border-0 !border-b !p-0 !pb-4 mb-4 dark:!bg-gray-800">
                <template #start>
                    <Link :href="route('admin.purchase_orders.create')">
                        <Button label="New Purchase Order" icon="pi pi-plus" severity="primary" />
                    </Link>
                </template>

                <template #end>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="search" placeholder="Search PO numbers..." class="w-full sm:w-64" />
                    </IconField>
                </template>
            </Toolbar>

            <DataTable :value="purchaseOrders.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(purchaseOrders.current_page - 1) * purchaseOrders.per_page" :rows="purchaseOrders.per_page"
                :totalRecords="purchaseOrders.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows>

                <template #empty> No purchase orders found. </template>
                <template #loading> Loading purchase orders... </template>
                <Column header="Actions" :exportable="false" style="min-width: 10rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-1">
                            <a :href="route('purchase_order.show', slotProps.data.id)" v-tooltip.top="'View PO'"
                                target="_blank">
                                <Button icon="pi pi-file" text rounded severity="secondary" />
                            </a>

                            <Button v-if="!slotProps.data.mutation && slotProps.data.payment_status === 'paid'"
                                icon="pi pi-sync" text rounded severity="success" v-tooltip.top="'Sync to Finance'"
                                @click="syncFinance(slotProps.data)" />
                            <i v-else-if="slotProps.data.mutation" class="pi pi-check-circle text-emerald-500 p-2"
                                v-tooltip.top="'Already Synced'" />

                            <Link :href="route('admin.purchase_orders.edit', slotProps.data.id)"
                                v-if="slotProps.data.status !== 'received' && slotProps.data.payment_status !== 'paid'">
                                <Button icon="pi pi-pencil" text rounded severity="info" v-tooltip.top="'Edit PO'" />
                            </Link>
                            <Button icon="pi pi-trash" text rounded severity="danger" v-tooltip.top="'Delete PO'"
                                @click="confirmDeletePO(slotProps.data)" v-if="slotProps.data.payment_status !== 'paid'" />
                        </div>
                    </template>
                </Column>
                <Column field="po_number" header="PO Number" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ slotProps.data.po_number }}</span>
                    </template>
                </Column>

                <Column field="supplier.name" header="Supplier" sortable style="min-width: 15rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white capitalize leading-tight mb-0.5">{{
                                slotProps.data.supplier?.name }}</span>
                            <span class="text-[10px] text-gray-400 font-medium tracking-tight">{{
                                slotProps.data.supplier?.contact_person || 'No contact' }}</span>
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
                        <span class="text-sm font-medium">{{ formatDateIndonesian(slotProps.data.created_at) }}</span>
                    </template>
                </Column>


            </DataTable>
        </div>
    </AdminLayout>
</template>
