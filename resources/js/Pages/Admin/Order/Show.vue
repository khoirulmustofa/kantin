<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { formatCurrencyIndo, formatDateIndonesian } from '@/Utils/formatter';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

const props = defineProps({
    order: Object,
    menu: String,
    title: String,
});

const invoiceRef = ref(null);
const isGenerating = ref(false);

const printInvoice = () => {
    window.print();
};

const downloadPDF = async () => {
    if (!invoiceRef.value) return;
    isGenerating.value = true;

    try {
        const canvas = await html2canvas(invoiceRef.value, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        });

        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save(`Invoice-${props.order.order_number}.pdf`);
    } catch (error) {
        console.error('Error generating PDF:', error);
    } finally {
        isGenerating.value = false;
    }
};

const downloadImage = async () => {
    if (!invoiceRef.value) return;
    isGenerating.value = true;

    try {
        const canvas = await html2canvas(invoiceRef.value, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        });

        const link = document.createElement('a');
        link.download = `Invoice-${props.order.order_number}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Error generating image:', error);
    } finally {
        isGenerating.value = false;
    }
};

const getStatusSeverity = (status) => {
    switch (status) {
        case 'completed': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        case 'processing': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
        case 'pending': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
        case 'cancelled': return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>

    <Head :title="`Order ${order.order_number}`" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8 px-4 sm:px-6 lg:px-8 transition-colors duration-300">
        <div class="max-w-4xl mx-auto mb-10">
            <!-- Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-2 mb-6 no-print">
                <Link :href="route('admin.orders.index')">
                    <Button icon="pi pi-arrow-left" label="Back to List" text />
                </Link>
                <div class="flex flex-wrap gap-2">
                    <Button icon="pi pi-print" label="Print" @click="printInvoice" severity="secondary" outlined />
                    <Button icon="pi pi-image" label="Save as Image" @click="downloadImage" severity="secondary"
                        outlined :loading="isGenerating" />
                    <Button icon="pi pi-file-pdf" label="Download PDF" @click="downloadPDF" severity="primary"
                        :loading="isGenerating" />
                </div>
            </div>

            <!-- Invoice Content -->
            <div ref="invoiceRef"
                class="relative bg-white dark:bg-gray-900 shadow-2xl rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-800 transition-colors duration-300">

                <!-- Watermark -->
                <div
                    class="absolute inset-0 flex items-center justify-center pointer-events-none select-none z-0 overflow-hidden">
                    <span
                        class="text-[70px] font-black uppercase tracking-[1em] opacity-[0.3] dark:opacity-[0.05] -rotate-[35deg] transform whitespace-nowrap"
                        :class="{
                            'text-green-500': order.payment_status === 'paid',
                            'text-red-500': order.payment_status === 'failed',
                            'text-amber-500': order.payment_status === 'unpaid'
                        }">
                        {{ order.payment_status }}
                    </span>
                </div>

                <!-- Header Section -->
                <div class="relative pt-5 sm:pt-8 px-4 sm:px-8 overflow-hidden">
                    <!-- Background Decoration -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full -mr-32 -mt-32 blur-3xl">
                    </div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/5 rounded-full -ml-24 -mb-24 blur-3xl">
                    </div>

                    <div class="relative flex flex-col sm:flex-row justify-between items-start gap-8">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2.5 bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/20">
                                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Koperasi
                                    Digital</span>
                            </div>
                            <h1
                                class="text-4xl font-black text-gray-900 dark:text-white mb-2 leading-none uppercase tracking-tighter">
                                Invoice</h1>
                            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">#{{ order.order_number
                            }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="mb-2">
                                <p class="font-black dark:text-gray-500 tracking-widest mb-1">
                                    Status</p>
                                <span :class="getStatusSeverity(order.status)"
                                    class="inline-block uppercase font-bold px-3 py-1 rounded-full text-[10px] shadow-sm">
                                    {{ order.status }}
                                </span>
                            </div>
                            <div>
                                <p class="font-black dark:text-gray-500 tracking-widest mb-1">
                                    Order Date</p>
                                <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{
                                    formatDateIndonesian(order.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-12 mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <div>
                            <p class="font-black text-gray-400 dark:text-gray-500 tracking-widest mb-2">
                                Billed To</p>
                            <div class="flex items-center gap-4 mb-3">
                                <Avatar :label="order.user?.name.charAt(0)" shape="circle"
                                    class="!bg-blue-500 !text-white !font-bold" />
                                <div>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white leading-none">{{
                                        order.user?.name }}</p>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ order.user?.email }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="font-black text-gray-400 dark:text-gray-500 tracking-widest mb-2">
                                Shipping Information</p>
                            <div class="flex items-start gap-3">
                                <i class="pi pi-map-marker text-emerald-500 mt-1 text-lg"></i>
                                <p
                                    class="text-base font-medium text-gray-700 dark:text-gray-300 leading-relaxed max-w-xs">
                                    {{ order.shipping_address }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="font-black text-gray-400 dark:text-gray-500 tracking-widest mb-2">
                                Payment Information</p>
                            <div class="flex flex-col items-start gap-1">
                                
                                <p
                                    class="text-base font-medium text-gray-700 dark:text-gray-300 leading-relaxed max-w-xs">
                                    <i class="pi pi-wallet text-emerald-500 mt-1 text-lg mr-2"></i>{{ order.financial_account?.name || '-' }}
                                </p>
                                <p class="text-base font-medium text-gray-700 dark:text-gray-300 leading-relaxed max-w-xs ml-6">No. Rekening : {{ order.financial_account?.account_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="px-4 sm:px-8">
                    <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-800">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th class="px-6 py-4 font-black uppercase tracking-widest">
                                        Product</th>
                                    <th class="px-6 py-4 font-black uppercase tracking-widest text-center">
                                        Qty</th>
                                    <th class="px-6 py-4 font-black uppercase tracking-widest text-right">
                                        Price</th>
                                    <th class="px-6 py-4 font-black uppercase tracking-widest text-right">
                                        Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                <tr v-for="item in order.order_items" :key="item.id"
                                    class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-gray-900 dark:text-white capitalize">{{
                                                    item.product?.name }}</span>
                                            <span class="text-xs text-gray-400 font-medium mt-0.5">{{
                                                item.product?.category?.name || 'Category' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8  text-sm font-bold text-gray-700 dark:text-gray-300">{{
                                                item.quantity }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right font-medium text-gray-700 dark:text-gray-300">
                                        {{ formatCurrencyIndo(item.selling_price) }}
                                    </td>
                                    <td class="px-6 py-5 text-right font-bold text-gray-900 dark:text-white">
                                        {{ formatCurrencyIndo(item.selling_price * item.quantity) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div class="p-4 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-12">
                        <div class="max-w-xs">
                            <p class="font-black uppercase tracking-widest mb-3">
                                Notes</p>
                            <p
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 leading-relaxed italic border-l-4 border-blue-500 pl-4 bg-white dark:bg-gray-900 p-4 rounded-r-xl shadow-sm">
                                Payment Method: {{ order.financial_account?.name || '-' }}
                                <span v-if="order.financial_account?.account_number" class="text-gray-400"> No: {{
                                    order.financial_account.account_number }}</span>.
                                <br />
                                Please keep this invoice as proof of purchase. Thank you for shopping with us!
                            </p>
                        </div>
                        <div class="w-full sm:w-auto min-w-[400px]">
                            <div class="flex flex-col gap-4">
                                <div class="flex justify-between items-center px-4 py-2">
                                    <span class="text-lg font-bold  uppercase tracking-tighter">Subtotal</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{
                                        formatCurrencyIndo(order.grand_total - order.shipping_cost) }}</span>
                                </div>
                                <div class="flex justify-between items-center px-4 py-2">
                                    <span class="text-lg font-bold uppercase tracking-tighter">Shipping</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{
                                        formatCurrencyIndo(order.shipping_cost) }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center px-6 py-5 rounded-2xl bg-emerald-600 shadow-xl shadow-emerald-600/20 text-white mt-2 ring-4 ring-emerald-500/10">
                                    <span class="text-sm font-black uppercase tracking-widest">Grand Total</span>
                                    <span class="text-3xl font-black">{{ formatCurrencyIndo(order.grand_total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p
                class="text-center text-xs font-bold text-gray-400 dark:text-gray-600 mt-12 uppercase tracking-[0.3em] no-print">
                Generated by Koperasi Digital System &copy; {{ new Date().getFullYear() }}</p>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        background-color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .invoice-card {
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}

/* Ensure premium font rendering */
.font-black {
    font-weight: 900;
}
</style>
