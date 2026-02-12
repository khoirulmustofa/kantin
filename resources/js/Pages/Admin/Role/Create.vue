<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    menu: String,
    title: String,
    permissions: Object, // Grouped by 'group'
});


const form = useForm({
    name: '',
    permissions: [],
});

const allPermissionNames = computed(() => {
    return Object.values(props.permissions).flat().map(p => p.name);
});

const isSelectAll = computed({
    get: () => form.permissions.length === allPermissionNames.value.length && allPermissionNames.value.length > 0,
    set: (val) => {
        if (val) {
            form.permissions = [...allPermissionNames.value];
        } else {
            form.permissions = [];
        }
    }
});

const toggleGroup = (groupName) => {
    const groupPermissions = props.permissions[groupName].map(p => p.name);
    const isAllSelected = checkGroup(groupName);

    if (isAllSelected) {
        // Deselect all in group
        form.permissions = form.permissions.filter(p => !groupPermissions.includes(p));
    } else {
        // Select all in group
        const newPermissions = [...form.permissions, ...groupPermissions];
        form.permissions = [...new Set(newPermissions)];
    }
};

const checkGroup = (groupName) => {
    const groupPermissions = props.permissions[groupName].map(p => p.name);
    return groupPermissions.every(p => form.permissions.includes(p));
};

const togglePermission = (name) => {
    if (form.permissions.includes(name)) {
        form.permissions = form.permissions.filter(p => p !== name);
    } else {
        form.permissions = [...form.permissions, name];
    }
};

const submit = () => {
    form.post(route('admin.roles.store'), {
        onSuccess: () => {
        },
    });
};
</script>

<template>

    <Head :title="props.title" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <form @submit.prevent="submit" class="mx-auto space-y-8 pb-20">
            <!-- Header Actions -->
            <div
                class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm sticky top-20 z-10 transition-colors">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.roles.index')">
                        <Button icon="pi pi-arrow-left" text rounded severity="secondary" />
                    </Link>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Create New Role</h2>
                </div>
                <div class="flex gap-3">
                    <Link :href="route('admin.roles.index')">
                        <Button label="Cancel" text severity="secondary" />
                    </Link>
                    <Button type="submit" label="Save Role" icon="pi pi-check" :loading="form.processing" />
                </div>
            </div>

            <!-- Basic Information -->
            <div
                class="card bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-6">

                <div class="flex flex-col gap-2">
                    <label for="name" class="text-sm font-bold text-gray-500 flex items-center gap-1">
                        Role Name <span class="text-rose-500">*</span>
                    </label>
                    <InputText id="name" v-model="form.name" placeholder="Enter role name (e.g. Manager)"
                        :class="{ 'p-invalid': form.errors.name }" class="!rounded-2xl !py-3 !px-4" />
                    <small v-if="form.errors.name" class="text-rose-500 font-medium">{{ form.errors.name }}</small>
                </div>
            </div>

            <!-- Permissions -->
            <div
                class="card bg-white dark:bg-gray-800 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-6">
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-6 bg-primary rounded-full"></div>
                        <h3 class="font-black text-lg text-gray-900 dark:text-white  tracking-tight">
                            Permissions</h3>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-white dark:bg-gray-800 py-2 px-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <span class="font-black  tracking-widest"
                            :class="isSelectAll ? 'text-green-600' : 'text-gray-400'">
                            {{ isSelectAll ? 'All Selected' : 'Select All' }}
                        </span>
                        <ToggleSwitch v-model="isSelectAll" />
                    </div>
                </div>

                <div v-if="form.errors.permissions"
                    class="bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 p-4 rounded-2xl border border-rose-100 dark:border-rose-900/50 text-sm font-bold flex items-center gap-2">
                    <i class="pi pi-exclamation-circle"></i>
                    {{ form.errors.permissions }}
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div v-for="(groupPermissions, groupName) in permissions" :key="groupName"
                        class="card bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">

                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 border-b border-gray-50 dark:border-gray-700/50 pb-4">
                            <h4 class="font-black text-gray-900 dark:text-white  tracking-tighter text-lg">{{
                                groupName }}</h4>
                            <Button @click="toggleGroup(groupName)"
                                :label="checkGroup(groupName) ? `Deselect all of ${groupName}` : `Select all of ${groupName}`"
                                :severity="checkGroup(groupName) ? 'danger' : 'secondary'" outlined size="small" rounded
                                class="!font-black !tracking-widest !px-6" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="permission in groupPermissions" :key="permission.id"
                                @click="togglePermission(permission.name)"
                                class="flex items-center justify-between p-4 rounded-2xl border transition-all cursor-pointer group"
                                :class="form.permissions.includes(permission.name)
                                    ? 'bg-primary/5 border-primary/20 ring-1 ring-primary/10'
                                    : 'bg-gray-50 dark:bg-gray-900/50 border-transparent hover:border-gray-200 dark:hover:border-gray-600'">

                                <span class="font-black  tracking-tight group-hover:translate-x-1 transition-transform"
                                    :class="form.permissions.includes(permission.name) ? 'text-primary' : 'text-gray-500 dark:text-gray-400'">
                                    {{ permission.name }}
                                </span>

                                <ToggleSwitch :modelValue="form.permissions.includes(permission.name)"
                                    @update:modelValue="togglePermission(permission.name)" @click.stop />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
:deep(.p-toggleswitch.p-highlight .p-toggleswitch-slider) {
    background: #0ea5e9;
}
</style>
