<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const toast = useToast();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Password updated successfully', life: 3000 });
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.$el.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.$el.focus();
            }
        },
    });
};
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3 border-b border-gray-50 dark:border-gray-700 pb-4">
            <div
                class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                <i class="pi pi-shield text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">Security
                    Settings</h2>
            </div>
        </div>

        <form @submit.prevent="updatePassword" class="space-y-6">
            <div class="grid grid-cols-1 gap-8">
                <div class="flex flex-col gap-2">
                    <label for="current_password" class="text-sm font-black tracking-widest">Current Password</label>
                    <Password id="current_password" v-model="form.current_password" :feedback="false" toggleMask
                        placeholder="••••••••"
                        inputClass="!w-full border-transparent focus:bg-white transition-all shadow-inner"
                        class="w-full" :class="{ 'p-invalid': form.errors.current_password }" />
                    <small v-if="form.errors.current_password" class="text-rose-500 font-bold px-4">{{
                        form.errors.current_password }}</small>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-sm font-black tracking-widest">New
                            Password</label>
                        <Password id="password" v-model="form.password" toggleMask placeholder="Min 8 characters"
                            inputClass="!w-full border-transparent focus:bg-white transition-all shadow-inner"
                            class="w-full" :class="{ 'p-invalid': form.errors.password }">
                            <template #header>
                                <h6 class="text-sm font-black tracking-widest mb-2">Pick a password</h6>
                            </template>
                            <template #footer>
                                <Divider />
                                <p class="mt-2 text-sm font-bold text-gray-600">Requirements:</p>
                                <ul
                                    class="pl-2 ml-2 mt-2 list-disc list-inside text-sm font-bold text-gray-700 space-y-1">
                                    <li>At least one lowercase</li>
                                    <li>At least one </li>
                                    <li>At least one numeric</li>
                                    <li>Minimum 8 characters</li>
                                </ul>
                            </template>
                        </Password>
                        <small v-if="form.errors.password" class="text-rose-500 font-bold px-4">{{ form.errors.password
                            }}</small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password_confirmation" class="text-sm font-black tracking-widest">Confirm
                            New Password</label>
                        <Password id="password_confirmation" v-model="form.password_confirmation" :feedback="false"
                            toggleMask placeholder="Repeat password"
                            inputClass="!w-full border-transparent focus:bg-white transition-all shadow-inner"
                            class="w-full" :class="{ 'p-invalid': form.errors.password_confirmation }" />
                        <small v-if="form.errors.password_confirmation" class="text-rose-500 font-bold px-4">{{
                            form.errors.password_confirmation }}</small>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <Button type="submit" label="Update Password" icon="pi pi-lock"
                    class="!rounded-2xl !py-3 !px-6 !bg-gray-900 !border-gray-900 hover:!bg-black !font-black !text-sm ! !tracking-widest shadow-xl shadow-gray-400 dark:shadow-none transition-all duration-300 active:scale-95"
                    :loading="form.processing" />
            </div>
        </form>
    </section>
</template>

<style scoped>
:deep(.p-password-input) {
    width: 100%;
}
</style>
