<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    status: Boolean,
    message: String,
    file: String,
    line: [String, Number],
    code: [String, Number],
});

const goHome = () => {
    router.visit('/');
};

const refreshPage = () => {
    window.location.reload();
};
</script>


<template>
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-800/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
        </div>

        <div
            class="relative z-10 w-full max-w-lg bg-white rounded-[40px] shadow-2xl border border-orange-100 overflow-hidden">
            <div class="p-8 md:p-12 text-center">

                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-orange-50 rounded-full mb-6 ring-8 ring-orange-50/50">
                    <i class="pi pi-server text-5xl text-orange-500"></i>
                </div>

                <h1 class="text-3xl font-black text-blue-900 mb-2">Oops! Server Error</h1>
                <p class="text-slate-500 mb-8">Terjadi kesalahan internal pada server kami. Silahkan coba beberapa saat
                    lagi atau hubungi administrator.</p>

                <div v-if="message" class="text-left bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-8">
                    <div
                        class="flex items-center gap-2 mb-2 text-orange-600 font-bold text-xs uppercase tracking-widest">
                        <i class="pi pi-code"></i>
                        <span>Debug Information</span>
                    </div>
                    <div class="space-y-1 font-mono text-[11px] text-slate-600 break-all">
                        <p><span class="font-bold text-blue-900 uppercase">Message:</span> {{ message }}</p>
                        <p><span class="font-bold text-blue-900 uppercase">File:</span> {{ file }}</p>
                        <p><span class="font-bold text-blue-900 uppercase">Line:</span> {{ line }}</p>
                        <p><span class="font-bold text-blue-900 uppercase">Code:</span> {{ code }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <Button label="Kembali ke Dashboard" icon="pi pi-home"
                        class="flex-1 p-button-raised bg-blue-800 border-none rounded-xl py-3 font-bold"
                        @click="goHome" />
                    <Button label="Muat Ulang" icon="pi pi-refresh"
                        class="flex-1 p-button-outlined p-button-secondary border-2 rounded-xl py-3 font-bold"
                        @click="refreshPage" />
                </div>
            </div>

            <div class="bg-blue-900 py-4 text-center">
                <p class="text-blue-200 text-[10px] font-medium uppercase tracking-[0.2em]">Error Code: 500 | Internal
                    Server Error</p>
            </div>
        </div>
    </div>
</template>


<style scoped>
/* Menyesuaikan warna PrimeVue Button secara manual agar pas dengan tema Biru Tua */
:deep(.p-button.bg-blue-800:hover) {
    background-color: #1e3a8a !important;
    /* Blue 900 */
}
</style>