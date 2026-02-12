<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProductForm from './Form.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    menu: String,
    title: String,
    products: Object,
    categories: Array,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const search = ref(props.filters.search || '');
const productDialog = ref(false);
const selectedProduct = ref({});
const isEdit = ref(false);
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.products.index'), {
        search: search.value,
        rows: extraParams.rows || props.products.per_page,
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
    selectedProduct.value = {};
    isEdit.value = false;
    productDialog.value = true;
};

const editProduct = (data) => {
    selectedProduct.value = { ...data };
    isEdit.value = true;
    productDialog.value = true;
};

const confirmDeleteProduct = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.name}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.products.destroy', data.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Deleted', life: 3000 });
                },
                onFinish: () => {
                    loading.value = false;
                }
            });
        }
    });
};

const duplicateProduct = (data) => {
    confirm.require({
        message: `Are you sure you want to duplicate ${data.name}?`,
        header: 'Confirm Duplicate',
        icon: 'pi pi-copy',
        accept: () => {
            loading.value = true;
            router.post(route('admin.products.duplicate', data.id), {}, {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Duplicated', life: 3000 });
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

    <ProductForm v-model:visible="productDialog" :product="selectedProduct" :categories="categories" :isEdit="isEdit" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <Toolbar class="!border-0 !border-b !p-0 !pb-2 dark:!bg-gray-800">
                <template #start>
                    <div class="w-full sm:w-auto">
                        <Button label="New Product" icon="pi pi-plus" severity="primary" @click="openNew"
                            class="w-full sm:w-auto" />
                    </div>
                </template>

                <template #end>
                    <div class="w-full flex justify-end">
                        <IconField class="w-full">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="search" placeholder="Search products..." class="w-full sm:w-64" />
                            <InputIcon v-if="search" @click="search = ''"
                                class="cursor-pointer hover:text-red-500 transition-colors"
                                style="right: 0.75rem; left: auto;">
                                <i class="pi pi-times" />
                            </InputIcon>
                        </IconField>
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="products.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(products.current_page - 1) * products.per_page" :rows="products.per_page"
                :totalRecords="products.total" scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 60rem">

                <template #empty> No products found. </template>
                <template #loading> Loading products... </template>
                <Column header="Actions" :exportable="false" style="min-width: 5rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-2">
                            <Button icon="pi pi-copy" text rounded severity="success"
                                @click="duplicateProduct(slotProps.data)" />
                            <Button icon="pi pi-pencil" text rounded severity="info"
                                @click="editProduct(slotProps.data)" />
                            <Button icon="pi pi-trash" text rounded severity="danger"
                                @click="confirmDeleteProduct(slotProps.data)" />
                        </div>

                    </template>
                </Column>

                <Column header="Image" style="min-width: 5rem">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <Avatar v-if="slotProps.data.images?.length" :image="slotProps.data.images[0].image_url"
                                shape="rounded" class="border border-gray-100 dark:border-gray-700 shadow-sm"
                                size="large" />
                            <div v-else
                                class="w-12 h-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 border border-dashed border-gray-200 dark:border-gray-700">
                                <i class="pi pi-image text-lg"></i>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column field="name" header="Name" sortable style="min-width: 15rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 dark:text-white capitalize leading-tight mb-0.5">{{
                                slotProps.data.name }}</span>
                            <span class="text-[10px] text-gray-400 font-medium tracking-tight line-clamp-1">{{
                                slotProps.data.description || 'No description' }}</span>
                        </div>
                    </template>
                </Column>

                <Column field="category.name" header="Category" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.category?.name" severity="secondary" rounded />
                    </template>
                </Column>

                <Column field="cost_price" header="Cost Price" sortable style="min-width: 10rem">
                    <template #body="slotProps">
                        {{ formatCurrencyIndo(slotProps.data.cost_price) }}
                    </template>
                </Column>

                <Column field="selling_price" header="Selling Price" sortable style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ formatCurrencyIndo(slotProps.data.selling_price) }}
                            </span>

                            <div v-if="slotProps.data.cost_price" class="text-xs mt-1">
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">
                                    +{{ formatCurrencyIndo(slotProps.data.selling_price - slotProps.data.cost_price) }}
                                </span>
                                <span class="text-gray-400 mx-1">|</span>
                                <span
                                    class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-1 rounded">
                                    {{ (((slotProps.data.selling_price - slotProps.data.cost_price) /
                                        slotProps.data.cost_price) * 100).toFixed(1) }}%
                                </span>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column field="stock" header="Stock" sortable style="min-width: 8rem">
                    <template #body="slotProps">
                        <span :class="{ 'text-red-500 font-bold': slotProps.data.stock <= 5 }">
                            {{ slotProps.data.stock }}
                        </span>
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
