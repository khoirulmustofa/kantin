<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from './Form.vue';
import { useConfirm } from "primevue/useconfirm";
import debounce from 'lodash/debounce';

const props = defineProps({
    menu: String,
    title: String,
    categories: Object,
    filters: Object,
});

const confirm = useConfirm();

const search = ref(props.filters.search || '');
const categoryDialog = ref(false);
const selectedCategory = ref({});
const isEdit = ref(false);
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.financial_categories.index'), {
        search: search.value,
        rows: extraParams.rows || props.categories.per_page,
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
    selectedCategory.value = {};
    isEdit.value = false;
    categoryDialog.value = true;
};

const editCategory = (data) => {
    selectedCategory.value = { ...data };
    isEdit.value = true;
    categoryDialog.value = true;
};

const confirmDeleteCategory = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.name}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.financial_categories.destroy', data.id), {
                onSuccess: () => {
                    setTimeout(() => {
                        page.props.flash.success = null;
                    }, 500);
                },
                onFinish: () => { loading.value = false }
            });
        }
    });
};

const getTypeSeverity = (type) => {
    return type === 'income' ? 'success' : 'danger';
};
</script>

<template>

    <Head :title="props.title" />

    <CategoryForm v-model:visible="categoryDialog" :category="selectedCategory" :isEdit="isEdit" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <Toolbar class="!border-0 !border-b !p-0 !pb-2 dark:!bg-gray-800">
                <template #start>
                    <div class="w-full sm:w-auto">
                        <Button label="New Category" icon="pi pi-plus" severity="primary" @click="openNew"
                            class="w-full sm:w-auto" />
                    </div>
                </template>

                <template #end>
                    <div class="w-full flex justify-end">
                        <IconField class="w-full">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search categories..." class="w-full sm:w-64" />
                            <InputIcon v-if="search" @click="search = ''"
                                class="cursor-pointer hover:text-red-500 transition-colors"
                                style="right: 0.75rem; left: auto;">
                                <i class="pi pi-times" />
                            </InputIcon>
                        </IconField>
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="categories.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(categories.current_page - 1) * categories.per_page" :rows="categories.per_page"
                :totalRecords="categories.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 40rem">

                <template #empty> No categories found. </template>
                <template #loading> Loading categories... </template>

                <Column header="Actions" :exportable="false" style="min-width: 5rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="info"
                                @click="editCategory(slotProps.data)" />
                            <Button icon="pi pi-trash" text rounded severity="danger"
                                @click="confirmDeleteCategory(slotProps.data)" />
                        </div>
                    </template>
                </Column>

                <Column field="name" header="Name" sortable style="min-width: 15rem">
                    <template #body="slotProps">
                        <span class="font-bold text-gray-900 dark:text-white tracking-tight">{{
                            slotProps.data.name }}</span>
                    </template>
                </Column>

                <Column field="type" header="Type" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.type" :severity="getTypeSeverity(slotProps.data.type)" rounded
                            class="uppercase text-[10px] font-black" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
