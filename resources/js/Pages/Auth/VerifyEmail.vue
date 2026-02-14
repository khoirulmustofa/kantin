<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const props = defineProps({
    status: {
        type: String,
    },
    menu: {
        type: String,
        default: 'verify-email'
    },
    title: {
        type: String,
        default: 'Email Verification'
    }
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
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
                            <i class="pi pi-envelope text-4xl text-emerald-600"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white tracking-tight">Verify Email</h1>
                        <p class="text-emerald-100 mt-2 text-sm">Check your inbox</p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="verificationLinkSent" class="mx-8 mt-6">
                        <Message severity="success" :closable="false">
                            A new verification link has been sent to the email address you provided during registration.
                        </Message>
                    </div>

                    <!-- Info Message -->
                    <div class="px-8" :class="verificationLinkSent ? 'pt-4' : 'pt-8'">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i class="pi pi-info-circle text-blue-500 text-xl mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        Thanks for signing up! Before getting started, could you verify your email
                                        address by clicking on the link we just emailed to you? If you didn't receive
                                        the email, we will gladly send you another.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        <!-- Resend Button -->
                        <Button type="submit" label="Resend Verification Email" icon="pi pi-send"
                            :loading="form.processing"
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

                        <!-- Logout Button -->
                        <Link :href="route('logout')" method="post" as="button"
                            class="w-full inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-red-200">
                            <i class="pi pi-sign-out text-lg"></i>
                            <span>Log Out</span>
                        </Link>
                    </form>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-8 space-y-3">
                    <p class="text-sm text-gray-600">
                        Didn't receive the email? Check your spam folder.
                    </p>
                    <p class="text-xs text-gray-500">
                        Need help?
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 font-semibold ml-1">
                            Contact Support
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>
