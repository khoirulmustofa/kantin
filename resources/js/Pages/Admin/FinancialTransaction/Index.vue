<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TransactionDetail from './Detail.vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import debounce from 'lodash/debounce';

const props = defineProps({
    menu: String,
    title: String,
    mutations: Object,
    accounts: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const flowFilter = ref(props.filters.flow || null);
const accountFilter = ref(props.filters.account_id || null);
const loading = ref(false);
const detailDialog = ref(false);
const selectedTransaction = ref(null);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const flowOptions = [
    { label: 'All Flows', value: null },
    { label: 'Income', value: 'in' },
    { label: 'Expense', value: 'out' }
];

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.financial_transactions.index'), {
        search: search.value,
        flow: flowFilter.value,
        account_id: accountFilter.value,
        rows: extraParams.rows || props.mutations.per_page,
        multiSortMeta: multiSortMeta.value.length ? JSON.stringify(multiSortMeta.value) : null,
        ...extraParams
    }, {
        preserveState: true,
        replace: true,
        onBefore: () => { loading.value = true },
        onFinish: () => { loading.value = false }
    });
};

watch([search, flowFilter, accountFilter], debounce(() => {
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

const viewDetail = (data) => {
    selectedTransaction.value = data;
    detailDialog.value = true;
};
</script>

<template>

    <Head :title="props.title" />

    <TransactionDetail v-model:visible="detailDialog" :transaction="selectedTransaction" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-4 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <!-- Filter Bar -->
            <div
                class="flex flex-col md:flex-row gap-4 mb-4 p-2 bg-gray-50/50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-800">
                <div class="flex-1">
                    <IconField class="w-full">
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="search" placeholder="Search transactions..." class="w-full" />
                    </IconField>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <Select v-model="flowFilter" :options="flowOptions" optionLabel="label" optionValue="value"
                        placeholder="Flow" class="w-full md:w-40" />
                    <Select v-model="accountFilter" :options="accounts" optionLabel="name" optionValue="id"
                        placeholder="Account" class="w-full md:w-48" :showClear="true" />
                </div>
            </div>

            <DataTable :value="mutations.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(mutations.current_page - 1) * mutations.per_page" :rows="mutations.per_page"
                :totalRecords="mutations.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[10, 25, 50]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm overflow-hidden" stripedRows
                tableStyle="min-width: 70rem">

                <template #empty> No transactions found. </template>

                <Column field="transaction_date" header="Date" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ new Date(slotProps.data.transaction_date).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short', year: 'numeric' }) }}
                        </span>
                    </template>
                </Column>

                <Column field="account.name" header="Account" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="pi pi-wallet text-gray-500"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-gray-100 italic tracking-tight">{{
                                slotProps.data.account?.name }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="category.name" header="Category" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <span
                            class="text-xs font-black uppercase tracking-widest text-gray-400 group-hover:text-emerald-500 transition-colors">
                            {{ slotProps.data.category?.name || 'Uncategorized' }}
                        </span>
                    </template>
                </Column>

                <Column field="amount" header="Amount" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span :class="[
                                'font-black text-lg tracking-tighter',
                                slotProps.data.flow === 'in' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                            ]">
                                {{ slotProps.data.flow === 'in' ? '+' : '-' }} {{
                                formatCurrencyIndo(slotProps.data.amount) }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column field="description" header="Description" style="min-width: 20rem">
                    <template #body="slotProps">
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed italic">
                            {{ slotProps.data.description }}
                        </p>
                    </template>
                </Column>

                <Column header="Detail" style="min-width: 5rem" class="text-center">
                    <template #body="slotProps">
                        <Button icon="pi pi-eye" text rounded severity="secondary"
                            @click="viewDetail(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>

<style scoped>
:deep(.p-datatable-sm .p-datatable-tbody > tr > td) {
    padding: 0.75rem 0.5rem;
}
</style>
