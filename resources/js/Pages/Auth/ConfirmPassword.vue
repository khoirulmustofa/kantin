<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const props = defineProps({
    menu: {
        type: String,
        default: 'confirm-password'
    },
    title: {
        type: String,
        default: 'Confirm Password'
    }
});

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
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
                            <i class="pi pi-shield text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Secure Area</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Confirm your password</p>
                    </div>

                    <!-- Info Message -->
                    <div class="px-8 pt-8">
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i class="pi pi-exclamation-triangle text-amber-500 text-xl mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        This is a secure area of the application. Please confirm your password before
                                        continuing.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password"
                                class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Password
                            </label>
                            <Password id="password" v-model="form.password" placeholder="Enter your password"
                                :feedback="false" toggleMask class="w-full" inputClass="w-full"
                                :class="{ 'p-invalid': form.errors.password }" required autofocus
                                autocomplete="current-password" />
                            <small v-if="form.errors.password" class="text-red-500 font-semibold">
                                {{ form.errors.password }}
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" label="Confirm" icon="pi pi-check-circle" :loading="form.processing"
                            class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 !text-white !font-bold !py-3 !text-base !rounded-xl !shadow-lg !shadow-emerald-200 hover:!shadow-xl hover:!shadow-emerald-300 transition-all" />
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-8">
                    <p class="text-xs text-gray-500">
                        Your password confirmation is required for security purposes.
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
