<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const page = usePage();

const props = defineProps({
    status: {
        type: String,
    },
    menu: {
        type: String,
        default: 'forgot-password'
    },
    title: {
        type: String,
        default: 'Forgot Password'
    }
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => {
            page.props.flash.success = "Password reset link sent to your email!";
            setTimeout(() => {
                page.props.flash.success = null;
            }, 500);
        }
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
                            <i class="pi pi-lock text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Forgot Password?</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Reset your password</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="mx-8 mt-6">
                        <Message severity="success" :closable="false">{{ status }}</Message>
                    </div>

                    <!-- Info Message -->
                    <div class="px-8 pt-8">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i class="pi pi-info-circle text-blue-500 text-xl mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        No problem! Just enter your email address and we'll send you a password reset
                                        link that will allow you to choose a new one.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email"
                                placeholder="Enter your registered email" class="w-full"
                                :class="{ 'p-invalid': form.errors.email }" required autofocus
                                autocomplete="username" />
                            <small v-if="form.errors.email" class="text-red-500 font-semibold">
                                {{ form.errors.email }}
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" label="Send Reset Link" icon="pi pi-send" :loading="form.processing"
                            class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 !text-white !font-bold !py-3 !text-base !rounded-xl !shadow-lg !shadow-emerald-200 hover:!shadow-xl hover:!shadow-emerald-300 transition-all" />

                        <!-- Back to Login -->
                        <div class="text-center pt-4">
                            <Link :href="route('login')"
                                class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="pi pi-arrow-left"></i>
                                <span>Back to Login</span>
                            </Link>
                        </div>
                    </form>
                </div>

                <!-- Additional Help -->
                <div class="text-center mt-8 space-y-3">
                    <p class="text-sm text-gray-600">
                        Still having trouble?
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold ml-1">
                            Contact Support
                        </a>
                    </p>
                    <p class="text-xs text-gray-500">
                        Remember your password?
                        <Link :href="route('login')" class="text-emerald-600 hover:text-emerald-700 font-semibold ml-1">
                            Sign In
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
