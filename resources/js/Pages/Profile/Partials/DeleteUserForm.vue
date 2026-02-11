<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.$el.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.$el.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-4">
        <div class="flex items-center gap-3 border-b border-gray-50 dark:border-gray-100/10 pb-2">
            <div
                class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                <i class="pi pi-exclamation-triangle text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">Danger Zone</h2>
               
            </div>
        </div>

        <div
            class="p-4 bg-rose-50/50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/30 space-y-4">
            <p class="text-sm font-bold text-gray-600 dark:text-gray-400 leading-relaxed">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Please be certain before proceeding.
            </p>

            <Button label="Delete Account" icon="pi pi-trash" severity="danger"
                class="!rounded-2xl !py-3 !px-6 !font-black !tracking-widest shadow-lg shadow-rose-500/20 active:scale-95 transition-all"
                @click="confirmUserDeletion" />
        </div>

        <Dialog v-model:visible="confirmingUserDeletion" modal header="Delete Account" :style="{ width: '30rem' }"
            class="custom-dialog" :pt="{
                root: { class: 'rounded-[2rem] overflow-hidden border-none shadow-2xl' },
                header: { class: 'bg-white dark:bg-gray-800 p-8 pb-0' },
                content: { class: 'bg-white dark:bg-gray-800 p-8' },
                footer: { class: 'bg-gray-50 dark:bg-gray-900/50 p-6' }
            }">

            <div class="space-y-4">
                <div
                    class="flex items-center gap-4 p-4 bg-rose-50 dark:bg-rose-900/20 rounded-2xl border border-rose-100 dark:border-rose-900/30">
                    <i class="pi pi-info-circle text-rose-600 text-xl"></i>
                    <p class="text-sm font-bold text-rose-700 dark:text-rose-400"> This action is permanent and cannot
                        be undone. </p>
                </div>

                <p class="text-sm font-bold  text-gray-400 tracking-widest">
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <div class="flex flex-col gap-2">
                    <Password id="password" ref="passwordInput" v-model="form.password" :feedback="false"
                        placeholder="Enter your password"
                        inputClass="!w-full !rounded-2xl !py-3 !px-3 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner"
                        class="w-full" @keyup.enter="deleteUser" :class="{ 'p-invalid': form.errors.password }" />
                    <small v-if="form.errors.password" class="text-rose-500 font-bold px-4">{{ form.errors.password
                        }}</small>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 w-full">
                    <Button label="Cancel" text severity="secondary" @click="closeModal"
                        class="!font-black !tracking-widest" />
                    <Button label="Delete Permanently" severity="danger" :loading="form.processing" @click="deleteUser"
                        class="!rounded-xl !px-6 !font-black !tracking-widest shadow-lg shadow-rose-500/20" />
                </div>
            </template>
        </Dialog>
    </section>
</template>

<style scoped>
:deep(.custom-dialog .p-dialog-title) {
    font-size: 1.25rem;
    font-weight: 900;
    text-transform: ;
    letter-spacing: -0.025em;
}

:deep(.p-password-input) {
    width: 100%;
}
</style>
