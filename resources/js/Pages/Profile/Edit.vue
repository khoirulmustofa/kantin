<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const activeSection = ref('profile');

const sections = [
    { id: 'profile', label: 'Profile Information', icon: 'pi pi-user', description: 'Update your personal details' },
    { id: 'security', label: 'Security', icon: 'pi pi-lock', description: 'Change your password' },
    { id: 'danger', label: 'Danger Zone', icon: 'pi pi-exclamation-triangle', description: 'Delete your account permanently' },
];
</script>

<template>

    <Head title="Profile" />

    <AdminLayout v-model:menuActive="activeSection" title="My Profile">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">


            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Navigation Sidebar -->
                <aside class="w-full lg:w-80 flex flex-col gap-2">
                    <button v-for="section in sections" :key="section.id" @click="activeSection = section.id"
                        class="group flex items-start gap-4 p-4 rounded-2xl transition-all duration-300 text-left"
                        :class="activeSection === section.id
                            ? 'bg-emerald-500 text-white shadow-xl shadow-emerald-500/20 translate-x-1'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:translate-x-1 border border-gray-100 dark:border-gray-700 shadow-sm'">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                            :class="activeSection === section.id ? 'bg-white/10' : 'bg-gray-100 dark:bg-gray-700'">
                            <i :class="[section.icon, 'text-lg']"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-black tracking-tight">{{ section.label }}
                            </h3>
                            <p class="text-xs opacity-90 font-medium leading-tight mt-1">{{ section.description }}</p>
                        </div>
                    </button>
                </aside>

                <!-- Profile Content -->
                <div class="flex-1">
                    <div class="space-y-8">
                        <!-- Profile Information -->
                        <div v-if="activeSection === 'profile'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
                                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                            </div>
                        </div>

                        <!-- Update Password -->
                        <div v-if="activeSection === 'security'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
                                <UpdatePasswordForm />
                            </div>
                        </div>

                        <!-- Delete User -->
                        <div v-if="activeSection === 'danger'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-500/10 p-4 rounded-2xl border border-rose-100 dark:border-rose-900/30 shadow-sm hover:shadow-rose-500/5 transition-all">
                                <DeleteUserForm />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped></style>
