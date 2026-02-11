<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from "primevue/usetoast";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const toast = useToast();

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route('profile.update'), {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Profile information updated successfully', life: 3000 });
        },
    });
};
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-4">
            <div
                class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center">
                <i class="pi pi-user-edit text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">Profile
                    Information</h2>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label for="name"
                        class="text-sm font-black  tracking-widest">FullName</label>
                    <InputText id="name" v-model="form.name" placeholder="Enter your name"
                        class="border-transparent focus:bg-white transition-all shadow-inner"
                        :class="{ 'p-invalid': form.errors.name }" required autofocus />
                    <small v-if="form.errors.name" class="text-rose-500 font-bold px-4">{{ form.errors.name }}</small>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="email" class="text-sm font-black  tracking-widest">Email
                        Address</label>
                    <InputText id="email" v-model="form.email" type="email" placeholder="email@example.com"
                        class="border-transparent focus:bg-white transition-all shadow-inner"
                        :class="{ 'p-invalid': form.errors.email }" required />
                    <small v-if="form.errors.email" class="text-rose-500 font-bold px-4">{{ form.errors.email }}</small>
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null"
                class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-2xl border border-amber-100 dark:border-amber-900/50">
                <p class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-2">
                    <i class="pi pi-exclamation-triangle"></i>
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="underline hover:text-amber-900 dark:hover:text-amber-300 ml-1">
                        Resend verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-black  text-green-600">
                    A new verification link has been sent.
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <Button type="submit" label="Save Details" icon="pi pi-check"
                    class="!rounded-2xl !py-3 !px-6 !bg-emerald-500 !border-emerald-500 hover:!bg-emerald-600 !font-black !text-sm ! !tracking-widest shadow-xl shadow-emerald-500/20 transition-all duration-300 active:scale-95"
                    :loading="form.processing" />
            </div>
        </form>
    </section>
</template>
