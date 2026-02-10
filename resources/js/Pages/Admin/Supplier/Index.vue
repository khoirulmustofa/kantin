<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SupplierForm from './Form.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';

const props = defineProps({
    menu: String,
    title: String,
    suppliers: Object,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const search = ref(props.filters.search || '');
const supplierDialog = ref(false);
const selectedSupplier = ref({});
const isEdit = ref(false);
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.suppliers.index'), {
        search: search.value,
        rows: extraParams.rows || props.suppliers.per_page,
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
    selectedSupplier.value = {};
    isEdit.value = false;
    supplierDialog.value = true;
};

const editSupplier = (data) => {
    selectedSupplier.value = { ...data };
    isEdit.value = true;
    supplierDialog.value = true;
};

const confirmDeleteSupplier = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.name}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.suppliers.destroy', data.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Supplier Deleted', life: 3000 });
                    loading.value = false;
                },
                onError: () => {
                    loading.value = false;
                }
            });
        }
    });
};
</script>

<template>

    <Head :title="props.title" />

    <SupplierForm v-model:visible="supplierDialog" :supplier="selectedSupplier" :isEdit="isEdit" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-4 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
            <Toolbar class="!border-0 !border-b !p-0 !pb-4 mb-4 dark:!bg-gray-800">
                <template #start>
                    <div class="flex gap-2">
                        <Button label="New Supplier" icon="pi pi-plus" severity="primary" @click="openNew" />
                    </div>
                </template>

                <template #end>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="search" placeholder="Search suppliers..." class="w-full sm:w-64" />
                    </IconField>
                </template>
            </Toolbar>

            <DataTable :value="suppliers.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(suppliers.current_page - 1) * suppliers.per_page" :rows="suppliers.per_page"
                :totalRecords="suppliers.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows>

                <template #empty> No suppliers found. </template>
                <template #loading> Loading suppliers... </template>

                <Column header="Actions" :exportable="false" style="min-width: 5rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="info"
                                @click="editSupplier(slotProps.data)" />
                            <Button icon="pi pi-trash" text rounded severity="danger"
                                @click="confirmDeleteSupplier(slotProps.data)" />
                        </div>
                    </template>
                </Column>
                <Column field="name" header="Name" sortable style="min-width: 15rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white capitalize leading-tight mb-0.5">{{
                                slotProps.data.name }}</span>
                            <span class="text-[10px] text-gray-400 font-medium tracking-tight">{{
                                slotProps.data.email || 'No email' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="contact_person" header="Contact" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold">{{ slotProps.data.contact_person || '-' }}</span>
                            <span class="text-[10px] text-gray-500">{{ slotProps.data.phone || '-' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="city" header="Location" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="text-sm">{{ slotProps.data.city || '-' }}</span>
                            <span class="text-[10px] text-gray-500 truncate max-w-[150px]">{{ slotProps.data.address ||
                                '-' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="is_active" header="Status" sortable style="min-width: 8rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.is_active ? 'Active' : 'Inactive'"
                            :severity="slotProps.data.is_active ? 'success' : 'danger'" rounded />
                    </template>
                </Column>


            </DataTable>
        </div>
    </AdminLayout>
</template>
