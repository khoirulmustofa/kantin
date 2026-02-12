<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import debounce from 'lodash/debounce';
import axios from 'axios';
import UserAssignment from './User.vue';


const props = defineProps({
    menu: String,
    title: String,
    roles: Object,
    filters: Object,
});

const confirm = useConfirm();

const displayUserModal = ref(false);
const selectedRole = ref(null);
const search = ref(props.filters.search || '');
const loading = ref(false);
const multiSortMeta = ref([]);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.roles.index'), {
        search: search.value,
        multiSortMeta: JSON.stringify(multiSortMeta.value),
        ...extraParams
    }, {
        preserveState: true,
        replace: true,
        onBefore: () => { loading.value = true },
        onFinish: () => { loading.value = false }
    });
};

const openUserAssignment = (role) => {
    selectedRole.value = role;
    displayUserModal.value = true;
};

watch(search, debounce((value) => {
    loadLazyData({ page: 1 });
}, 300));

const onPage = (event) => {
    loadLazyData({
        page: event.page + 1,
        rows: event.rows
    });
};

const onSort = (event) => {
    multiSortMeta.value = event.multiSortMeta;
    loadLazyData({ page: 1 });
};


const deleteRole = (data) => {
    confirm.require({
        message: `Are you sure you want to delete ${data.name}?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            loading.value = true;
            router.delete(route('admin.roles.destroy', data.id), {
                onSuccess: () => {
                },
                onError: (errors) => {
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
            class="card p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
            <Toolbar class="!border-0 !border-b !p-0 !pb-4 dark:!bg-gray-800">
                <template #start>
                    <Link :href="route('admin.roles.create')">
                        <Button label="New Role" icon="pi pi-plus" severity="primary" class="w-full sm:w-auto" />
                    </Link>
                </template>

                <template #end>
                    <IconField>
                        <InputIcon><i class="pi pi-search" /></InputIcon>
                        <InputText v-model="search" placeholder="Search roles..." class="w-full sm:w-64" />
                    </IconField>
                </template>
            </Toolbar>

            <DataTable :value="roles.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
                :first="(roles.current_page - 1) * roles.per_page" :rows="roles.per_page" :totalRecords="roles.total"
                scrollable
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}" @page="onPage" @sort="onSort"
                :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows tableStyle="min-width: 70rem">

                <template #empty> No roles found. </template>
                <template #loading> Loading roles data... </template>
                <Column header="Actions" :exportable="false" style="min-width: 10rem" class="text-center">
                    <template #body="slotProps">
                        <div class="flex justify-end gap-2">
                            <Button icon="pi pi-users" text rounded severity="success"
                                @click="openUserAssignment(slotProps.data)" tooltip="Assign Users" />
                            <Link :href="route('admin.roles.edit', slotProps.data.id)">
                                <Button icon="pi pi-pencil" text rounded severity="info" />
                            </Link>
                            <Button v-if="slotProps.data.name !== 'Admin'" icon="pi pi-trash" text rounded
                                severity="danger" @click="deleteRole(slotProps.data)" />
                        </div>
                    </template>
                </Column>

                <Column field="name" header="Role Name" sortable style="min-width: 14rem">
                    <template #body="slotProps">
                        <span class="font-bold text-gray-900 dark:text-white uppercase tracking-tight">{{
                            slotProps.data.name }}</span>
                    </template>
                </Column>

                <Column field="permissions_count" header="Permissions" style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="`${slotProps.data.permissions_count} Permissions`" severity="secondary" rounded />
                    </template>
                </Column>

                <Column field="users_count" header="Users" style="min-width: 10rem">
                    <template #body="slotProps">
                        <Tag :value="`${slotProps.data.users_count} Users`" severity="info" rounded
                            class="cursor-pointer" @click="openUserAssignment(slotProps.data)" />
                    </template>
                </Column>


            </DataTable>

            <!-- User Assignment Modal -->
            <UserAssignment v-model:visible="displayUserModal" :role="selectedRole" @saved="loadLazyData" />

        </div>
    </AdminLayout>
</template>
