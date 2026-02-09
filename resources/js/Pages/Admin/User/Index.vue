<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UserForm from './Form.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';

const props = defineProps({
    menu: String,
    title: String,
    users: Object, // Berisi data paginasi dari Laravel
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const search = ref(props.filters.search || '');
const userDialog = ref(false);
const selectedUser = ref({});
const isEdit = ref(false);
const loading = ref(false);
const multiSortMeta = ref(props.filters.multiSortMeta ? JSON.parse(props.filters.multiSortMeta) : []);

// Combined function to load data based on state
const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.users.index'), {
        search: search.value,
        rows: extraParams.rows || props.users.per_page,
        multiSortMeta: multiSortMeta.value.length ? JSON.stringify(multiSortMeta.value) : null,
        ...extraParams
    }, {
        preserveState: true,
        replace: true,
        onBefore: () => { loading.value = true },
        onFinish: () => { loading.value = false }
    });
};

// Server-side Search (Laravel) dengan Debounce
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
    selectedUser.value = {};
    isEdit.value = false;
    userDialog.value = true;
};

const editUser = (data) => {
    selectedUser.value = { ...data };
    isEdit.value = true;
    userDialog.value = true;
};

const confirmDeleteUser = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.name}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.users.destroy', data.id), {
                onSuccess: () => {
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'User Deleted', life: 3000 });
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

    <UserForm v-model:visible="userDialog" :user="selectedUser" :isEdit="isEdit" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">

        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">

            <Toolbar class="!border-0 !border-b !p-0 !pb-2 dark:!bg-gray-800">

                <template #start>
                    <div class="w-full sm:w-auto">
                        <Button label="New User" icon="pi pi-plus" severity="primary" @click="openNew"
                            class="w-full sm:w-auto" />
                    </div>
                </template>

                <template #end>
                    <div class="w-full flex justify-end">
                        <IconField class="w-full">
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>

                            <InputText v-model="search" placeholder="Search..." class="w-full sm:w-64" />

                            <InputIcon v-if="search" @click="search = ''"
                                class="cursor-pointer hover:text-red-500 transition-colors"
                                style="right: 0.75rem; left: auto;">
                                <i class="pi pi-times" />
                            </InputIcon>
                        </IconField>
                    </div>
                </template>
            </Toolbar>

            <DataTable :value="users.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(users.current_page - 1) * users.per_page" :rows="users.per_page" :totalRecords="users.total"
                scrollable paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport
            RowsPerPageDropdown" :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 50rem">
                <template #empty> No users found. </template>
                <template #loading> Loading user data... </template>

                <Column field="name" header="Name" sortable style="min-width: 14rem">
                    <template #body="slotProps">
                        <span class="font-bold text-gray-900 dark:text-white">{{ slotProps.data.name }}</span>
                    </template>
                </Column>
                <Column field="email" header="Email" sortable style="min-width: 14rem"></Column>

                <Column field="role" header="Role" style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.role"
                            :severity="slotProps.data.role === 'admin' ? 'success' : 'info'" rounded />
                    </template>
                </Column>

                <Column header="Actions" :exportable="false" style="min-width: 10rem" class="text-right">
                    <template #body="slotProps">
                        <div class="flex justify-end gap-2">
                            <Button icon="pi pi-pencil" text rounded severity="info"
                                @click="editUser(slotProps.data)" />
                            <Button icon="pi pi-trash" text rounded severity="danger"
                                @click="confirmDeleteUser(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>