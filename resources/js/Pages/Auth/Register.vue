<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const page = usePage();

const props = defineProps({
    menu: {
        type: String,
        default: 'register'
    },
    title: {
        type: String,
        default: 'Register'
    }
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onSuccess: () => {
            page.props.flash.success = "Registration successful! Please login.";
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
                            <i class="pi pi-user-plus text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Create Account</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Join us today</p>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Name Field -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Full Name
                            </label>
                            <InputText id="name" v-model="form.name" type="text" placeholder="Enter your full name"
                                class="w-full" :class="{ 'p-invalid': form.errors.name }" required autofocus
                                autocomplete="name" />
                            <small v-if="form.errors.name" class="text-red-500 font-semibold">
                                {{ form.errors.name }}
                            </small>
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-wider">
                                Email Address
                            </label>
                            <InputText id="email" v-model="form.email" type="email" placeholder="Enter your email"
                                class="w-full" :class="{ 'p-invalid': form.errors.email }" required
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
                            <Password id="password" v-model="form.password" placeholder="Create a password" toggleMask
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
                                Confirm Password
                            </label>
                            <Password id="password_confirmation" v-model="form.password_confirmation"
                                placeholder="Confirm your password" :feedback="false" toggleMask class="w-full"
                                inputClass="w-full" :class="{ 'p-invalid': form.errors.password_confirmation }" required
                                autocomplete="new-password" />
                            <small v-if="form.errors.password_confirmation" class="text-red-500 font-semibold">
                                {{ form.errors.password_confirmation }}
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" label="Create Account" icon="pi pi-user-plus" :loading="form.processing"
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

                        <!-- Google Login Button -->
                        <Button as="a" href="/auth/google" severity="secondary" variant="outlined"
                            class="w-full flex items-center justify-center gap-3 !border-red-400 !border-2 !py-3 !rounded-2xl hover:!bg-red-50 transition-all">
                            <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5" alt="Google Logo">
                            <span class="font-bold tracking-tight">Sign up with Google</span>
                        </Button>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Already have an account?
                                <Link :href="route('login')"
                                    class="font-bold text-emerald-600 hover:text-emerald-700 transition-colors ml-1">
                                    Sign In
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-8">
                    <p class="text-xs text-gray-500">
                        By creating an account, you agree to our
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Terms of Service</a>
                        and
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
