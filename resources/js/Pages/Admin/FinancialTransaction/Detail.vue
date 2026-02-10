<script setup>
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    transaction: Object,
});

const visible = defineModel('visible', { type: Boolean, default: false });

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" header="Transaction Details" :modal="true"
        class="p-fluid overflow-hidden" :draggable="false">
        <div v-if="transaction" class="flex flex-col gap-6">
            <!-- Header Info -->
            <div
                class="flex flex-col items-center justify-center p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800">
                <div :class="[
                    'w-16 h-16 rounded-full flex items-center justify-center mb-4 shadow-inner capitalize text-xl font-black',
                    transaction.flow === 'in' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400'
                ]">
                    {{ transaction.flow }}
                </div>
                <h2 :class="[
                    'text-3xl font-black tracking-tighter mb-1',
                    transaction.flow === 'in' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'
                ]">
                    {{ transaction.flow === 'in' ? '+' : '-' }} {{ formatCurrencyIndo(transaction.amount) }}
                </h2>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">{{
                    transaction.category?.name }}</span>
            </div>

            <!-- Meta Data Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Date</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{
                        formatDate(transaction.transaction_date) }}</span>
                </div>
                <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <span
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Account</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ transaction.account?.name
                        }}</span>
                </div>
            </div>

            <!-- Description -->
            <div class="flex flex-col gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaction
                    Description</span>
                <div
                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 text-xs text-gray-600 dark:text-gray-400 leading-relaxed italic border border-gray-100 dark:border-gray-800">
                    {{ transaction.description || 'No description provided.' }}
                </div>
            </div>

            <!-- Image/Proof Area -->
            <div v-if="transaction.proof_image_url" class="flex flex-col gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaction Proof</span>
                <div
                    class="relative group rounded-2xl overflow-hidden border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-emerald-500 transition-colors">
                    <Image :src="transaction.proof_image_url" alt="Proof" width="100%" preview />
                </div>
            </div>

            <!-- Internal IDs / Meta -->
            <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                <div
                    class="flex justify-between items-center text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                    <span>TX-ID: {{ transaction.id.substring(0, 8) }}...</span>
                    <span>Ref: {{ transaction.reference_type?.split('\\').pop() || 'Manual' }}</span>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-center pt-2">
                <Button label="Close Detail" @click="visible = false"
                    class="w-full rounded-xl bg-gray-900 border-gray-900 hover:bg-black" />
            </div>
        </template>
    </Dialog>
</template>

<style scoped>
:deep(.p-dialog-header) {
    padding-bottom: 0.5rem;
}

:deep(.p-dialog-content) {
    scrollbar-width: none;
}

:deep(.p-dialog-content::-webkit-scrollbar) {
    display: none;
}
</style>
