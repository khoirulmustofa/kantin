<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import OutflowForm from './Form.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    menu: String,
    title: String,
    mutations: Object,
    accounts: Array,
    categories: Array,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const search = ref(props.filters.search || '');
const outflowDialog = ref(false);
const selectedMutation = ref({});
const isEdit = ref(false);
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.financial_cash_out.index'), {
        search: search.value,
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

const openNew = () => {
    selectedMutation.value = {};
    isEdit.value = false;
    outflowDialog.value = true;
};

const editMutation = (data) => {
    selectedMutation.value = { ...data };
    isEdit.value = true;
    outflowDialog.value = true;
};

const confirmDeleteAction = (data) => {
    confirm.require({
        message: `Are you sure you want to delete this outflow of ${formatCurrencyIndo(data.amount)}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.financial_cash_out.destroy', data.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Outflow Deleted', life: 3000 });
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

    <OutflowForm v-model:visible="outflowDialog" :mutation="selectedMutation" :isEdit="isEdit" :accounts="accounts"
        :categories="categories" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <Toolbar class="!border-0 !border-b !p-0 !pb-2 dark:!bg-gray-800">
                <template #start>
                    <div class="w-full sm:w-auto">
                        <Button label="New Outflow" icon="pi pi-minus" severity="danger" @click="openNew"
                            class="w-full sm:w-auto" />
                    </div>
                </template>

                <template #end>
                    <div class="w-full flex justify-end">
                        <IconField class="w-full">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search outflows..." class="w-full sm:w-64" />
                            <InputIcon v-if="search" @click="search = ''"
                                class="cursor-pointer hover:text-red-500 transition-colors"
                                style="right: 0.75rem; left: auto;">
                                <i class="pi pi-times" />
                            </InputIcon>
                        </IconField>
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="mutations.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(mutations.current_page - 1) * mutations.per_page" :rows="mutations.per_page"
                :totalRecords="mutations.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 70rem">

                <template #empty> No cash outflows found. </template>
                <template #loading> Loading outflow data... </template>

                <Column header="Actions" :exportable="false" style="min-width: 7rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="info"
                                @click="editMutation(slotProps.data)" />
                            <Button icon="pi pi-trash" text rounded severity="danger"
                                @click="confirmDeleteAction(slotProps.data)" />
                        </div>
                    </template>
                </Column>

                <Column field="transaction_date" header="Date" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <span class="text-sm font-medium">{{ new
                            Date(slotProps.data.transaction_date).toLocaleDateString('id-ID') }}</span>
                    </template>
                </Column>

                <Column field="account.name" header="Account" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <i class="pi pi-wallet text-gray-400"></i>
                            <span class="font-bold text-gray-900 dark:text-white">{{ slotProps.data.account?.name
                                }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="category.name" header="Category" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.category?.name" severity="danger" rounded
                            class="uppercase text-[10px]" />
                    </template>
                </Column>

                <Column field="amount" header="Amount" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <span class="font-black text-rose-600 dark:text-rose-400">- {{
                            formatCurrencyIndo(slotProps.data.amount) }}</span>
                    </template>
                </Column>

                <Column field="description" header="Description" style="min-width: 20rem">
                    <template #body="slotProps">
                        <span class="text-xs line-clamp-2 text-gray-500 dark:text-gray-400">{{
                            slotProps.data.description }}</span>
                    </template>
                </Column>

                <Column header="Proof" style="min-width: 5rem">
                    <template #body="slotProps">
                        <Image v-if="slotProps.data.proof_image_url" :src="slotProps.data.proof_image_url" alt="Proof"
                            width="40" preview class="rounded-lg shadow-sm border border-gray-100" />
                        <div v-else class="text-gray-300 italic text-[10px]">No proof</div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
