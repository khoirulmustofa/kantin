<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { useToast } from "primevue/usetoast";
import debounce from 'lodash/debounce';

const props = defineProps({
    role: Object,
});

const visible = defineModel('visible', { type: Boolean });
const emit = defineEmits(['saved']);

const toast = useToast();
const users = ref({
    data: [],
    total: 0,
    current_page: 1,
    per_page: 5
});

const search = ref('');
const loading = ref(false);
const togglingId = ref(null);
const multiSortMeta = ref([]);

const loadUsers = async (page = 1, sortMeta = []) => {
    if (!props.role) return;
    loading.value = true;
    try {
        const response = await axios.get(route('admin.roles.users', props.role.id), {
            params: {
                page: page,
                search: search.value,
                rows: users.value.per_page,
                multiSortMeta: JSON.stringify(sortMeta)
            }
        });
        users.value = response.data.users;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load users', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    users.value.per_page = event.rows;
    loadUsers(event.page + 1, multiSortMeta.value);
};

const onSort = (event) => {
    multiSortMeta.value = event.multiSortMeta;
    loadUsers(1, multiSortMeta.value);
};

const onSearch = debounce(() => {
    loadUsers(1, multiSortMeta.value);
}, 300);

const toggleUser = async (user) => {
    togglingId.value = user.id;
    const action = !user.has_role ? 'assign' : 'remove';

    try {
        const response = await axios.post(route('admin.roles.users.toggle', props.role.id), {
            user_id: user.id,
            action: action
        });

        if (response.data.status) {
            user.has_role = !user.has_role;
            toast.add({ severity: 'success', summary: 'Success', detail: response.data.message, life: 2000 });
            emit('saved'); // Refresh parent users_count
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Failed to update user role',
            life: 3000
        });
    } finally {
        togglingId.value = null;
    }
};

watch(() => props.role, () => {
    if (props.role) loadUsers(1);
}, { immediate: true });

watch(search, () => {
    onSearch();
});
</script>

<template>
    <Dialog v-model:visible="visible" :header="`Manage Access - ${props.role?.name}`" :style="{ width: '60vw' }" modal
        :dismissableMask="true">

        <div class="mb-4">
            <IconField>
                <InputIcon><i class="pi pi-search" /></InputIcon>
                <InputText v-model="search" placeholder="Search by name or email..." class="w-full" />
            </IconField>
        </div>

        <DataTable :value="users.data" lazy paginator sortMode="multiple" :multiSortMeta="multiSortMeta"
            :first="(users.current_page - 1) * users.per_page" :rows="users.per_page" :totalRecords="users.total"
            scrollable
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
            @page="onPage" @sort="onSort" :loading="loading" dataKey="id" class="p-datatable-sm" stripedRows
            tableStyle="min-width: 40rem">

            <template #empty> No users found. </template>
            <template #loading> Loading users data... </template>

            <Column header="Access" style="width: 5rem" class="text-center">
                <template #body="slotProps">
                    <ToggleSwitch :modelValue="slotProps.data.has_role" @update:modelValue="toggleUser(slotProps.data)"
                        :disabled="togglingId === slotProps.data.id" />
                </template>
            </Column>

            <Column field="name" header="Name" sortable>
                <template #body="slotProps">
                    <span class="font-bold text-gray-900 dark:text-white">{{ slotProps.data.name }}</span>
                </template>
            </Column>

            <Column field="email" header="Email" sortable>
                <template #body="slotProps">
                    <span class="text-sm text-gray-500">{{ slotProps.data.email }}</span>
                </template>
            </Column>
        </DataTable>

        <template #footer>
            <Button label="Close" icon="pi pi-times" @click="visible = false" class="p-button-text" />
        </template>
    </Dialog>
</template>

<style scoped>
:deep(.p-datatable-tbody > tr > td) {
    padding: 0.75rem 1rem;
}
</style>
