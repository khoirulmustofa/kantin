<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { useAuthStore } from '@/Stores/authStore';


const authStore = useAuthStore();

const page = usePage();

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    menu: {
        type: String,
        default: 'login'
    },
    title: {
        type: String,
        default: 'Login'
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => {
            authStore.fetchAuth();
            page.props.flash.success = "Success Login";

            setTimeout(() => {
                page.props.flash.success = null;
            }, 500);
        },
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="min-h-[80vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-green-50 via-white to-blue-50">
            <div class="w-full max-w-md">
                <!-- Card Container -->
                <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-10 text-center">
                        <div
                            class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="pi pi-user text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Welcome Back</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Sign in to your account</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="mx-8 mt-6">
                        <Message severity="success" :closable="false">{{ status }}</Message>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email" placeholder="Enter your email"
                                class="w-full" :class="{ 'p-invalid': form.errors.email }" required autofocus
                                autocomplete="username" />
                            <small v-if="form.errors.email" class="text-red-500 font-semibold">
                                {{ form.errors.email }}
                            </small>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password"
                                class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Password
                            </label>
                            <Password id="password" v-model="form.password" placeholder="Enter your password"
                                :feedback="false" toggleMask class="w-full" inputClass="w-full"
                                :class="{ 'p-invalid': form.errors.password }" required
                                autocomplete="current-password" />
                            <small v-if="form.errors.password" class="text-red-500 font-semibold">
                                {{ form.errors.password }}
                            </small>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="form.remember" inputId="remember" :binary="true" />
                                <label for="remember" class="text-sm text-gray-600 font-semibold cursor-pointer">
                                    Remember me
                                </label>
                            </div>

                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                                Forgot Password?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" label="Sign In" icon="pi pi-sign-in" :loading="form.processing"
                            class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 !text-white !font-bold !py-3 !text-base !rounded-xl !shadow-lg !shadow-emerald-200 hover:!shadow-xl hover:!shadow-emerald-300 transition-all" />

                        <!-- Divider -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500 font-semibold">OR</span>
                            </div>
                        </div>
                       

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account?
                                <Link :href="route('register')"
                                    class="font-bold text-emerald-600 hover:text-emerald-700 transition-colors ml-1">
                                    Create Account
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-8">
                    <p class="text-xs text-gray-500">
                        By signing in, you agree to our
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Terms of Service</a>
                        and
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
