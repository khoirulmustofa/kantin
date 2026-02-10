<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';

const props = defineProps({
    menu: String,
    title: String,
    roles: Object,
    filters: Object,
});

const confirm = useConfirm();
const toast = useToast();

const search = ref(props.filters.search || '');
const loading = ref(false);

const loadLazyData = (extraParams = {}) => {
    router.get(route('admin.roles.index'), {
        search: search.value,
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
        page: event.page + 1
    });
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
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Role Deleted', life: 3000 });
                },
                onError: (errors) => {
                    toast.add({ severity: 'error', summary: 'Error', detail: errors.message || 'Error deleting role', life: 3000 });
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

            <DataTable :value="roles.data" :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows
                tableStyle="min-width: 50rem">
                <template #empty> No roles found. </template>

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

                <Column header="Actions" :exportable="false" style="min-width: 8rem" class="text-right">
                    <template #body="slotProps">
                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.roles.edit', slotProps.data.id)">
                                <Button icon="pi pi-pencil" text rounded severity="info" />
                            </Link>
                            <Button v-if="slotProps.data.name !== 'Admin'" icon="pi pi-trash" text rounded
                                severity="danger" @click="deleteRole(slotProps.data)" />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div class="mt-4 flex justify-between items-center">
                <span class="text-sm text-gray-500 font-medium">Showing {{ roles.from }} to {{ roles.to }} of {{
                    roles.total }}
                    roles</span>
                <nav v-if="roles.links.length > 3" class="flex gap-1">
                    <Link v-for="(link, k) in roles.links" :key="k" :href="link.url || '#'"
                        class="px-3 py-1 rounded-lg border text-sm transition-all shadow-sm" :class="[
                            link.active ? 'bg-primary border-primary text-white' : 'bg-white border-gray-200 text-gray-700 hover:border-primary hover:text-primary',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]" v-html="link.label" />
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>
