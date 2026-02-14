<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const page = usePage();

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    menu: {
        type: String,
        default: 'reset-password'
    },
    title: {
        type: String,
        default: 'Reset Password'
    }
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            page.props.flash.success = "Password reset successful! Please login with your new password.";
            setTimeout(() => {
                page.props.flash.success = null;
            }, 500);
        },
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="min-h-[80vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-emerald-50 via-white to-blue-50">
            <div class="w-full max-w-md">
                <!-- Card Container -->
                <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-10 text-center">
                        <div
                            class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="pi pi-key text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Reset Password</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Create a new password</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Email Field (Read-only) -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email" class="w-full"
                                :class="{ 'p-invalid': form.errors.email }" required autofocus autocomplete="username"
                                readonly disabled />
                            <small v-if="form.errors.email" class="text-red-500 font-semibold">
                                {{ form.errors.email }}
                            </small>
                        </div>

                        <!-- New Password Field -->
                        <div class="space-y-2">
                            <label for="password"
                                class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                New Password
                            </label>
                            <Password id="password" v-model="form.password" placeholder="Enter new password" toggleMask
                                class="w-full" inputClass="w-full" :class="{ 'p-invalid': form.errors.password }"
                                required autocomplete="new-password">
                                <template #footer>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Password must be at least 8 characters
                                    </p>
                                </template>
                            </Password>
                            <small v-if="form.errors.password" class="text-red-500 font-semibold">
                                {{ form.errors.password }}
                            </small>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="space-y-2">
                            <label for="password_confirmation"
                                class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Confirm New Password
                            </label>
                            <Password id="password_confirmation" v-model="form.password_confirmation"
                                placeholder="Confirm new password" :feedback="false" toggleMask class="w-full"
                                inputClass="w-full" :class="{ 'p-invalid': form.errors.password_confirmation }" required
                                autocomplete="new-password" />
                            <small v-if="form.errors.password_confirmation" class="text-red-500 font-semibold">
                                {{ form.errors.password_confirmation }}
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" label="Reset Password" icon="pi pi-check" :loading="form.processing"
                            class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 !text-white !font-bold !py-3 !text-base !rounded-xl !shadow-lg !shadow-emerald-200 hover:!shadow-xl hover:!shadow-emerald-300 transition-all" />
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-8">
                    <p class="text-xs text-gray-500">
                        Make sure to choose a strong password that you haven't used before.
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
