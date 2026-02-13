<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { formatCurrencyIndo, formatDateIndonesian } from '@/Utils/formatter';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const props = defineProps({
    purchaseOrder: Object,
    menu: String,
    title: String,
});

const page = usePage();

const invoiceRef = ref(null);
const isGenerating = ref(false);

const printPurchaseOrder = () => {
    window.print();
};

const downloadPDF = async () => {
    if (!invoiceRef.value) return;
    isGenerating.value = true;

    const scrollX = window.scrollX;
    const scrollY = window.scrollY;
    window.scrollTo(0, 0);

    try {
        const canvas = await html2canvas(invoiceRef.value, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            windowWidth: 1200,
            onclone: (clonedDoc) => {
                const element = clonedDoc.querySelector('html');
                if (element) {
                    element.classList.remove('my-app-dark');
                }
                return new Promise((resolve) => {
                    if (clonedDoc.fonts && clonedDoc.fonts.ready) {
                        clonedDoc.fonts.ready.then(resolve);
                    } else {
                        setTimeout(resolve, 500);
                    }
                });
            }
        });

        const imgData = canvas.toDataURL('image/png', 1.0);
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        const margin = 10;
        const pdfWidth = pdf.internal.pageSize.getWidth() - (margin * 2);
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

        pdf.addImage(imgData, 'PNG', margin, margin, pdfWidth, pdfHeight, undefined, 'FAST');
        pdf.save(`PO-${props.purchaseOrder?.po_number || 'Detail'}.pdf`);
    } catch (error) {
        console.error('Error generating PDF:', error);
    } finally {
        window.scrollTo(scrollX, scrollY);
        isGenerating.value = false;
    }
};

const downloadImage = async () => {
    if (!invoiceRef.value) return;
    isGenerating.value = true;

    const scrollX = window.scrollX;
    const scrollY = window.scrollY;
    window.scrollTo(0, 0);

    try {
        const canvas = await html2canvas(invoiceRef.value, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            windowWidth: 1200,
            onclone: (clonedDoc) => {
                const element = clonedDoc.querySelector('html');
                if (element) {
                    element.classList.remove('my-app-dark');
                }
            }
        });

        const link = document.createElement('a');
        link.download = `PO-${props.purchaseOrder?.po_number || 'Detail'}.png`;
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
    } catch (error) {
        console.error('Error generating image:', error);
    } finally {
        window.scrollTo(scrollX, scrollY);
        isGenerating.value = false;
    }
};

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href).then(() => {
        page.props.flash.success = 'Link copied to clipboard';
        setTimeout(() => {
            page.props.flash.success = null;
        }, 500);
    });
};

</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div v-if="props.purchaseOrder" class="bg-gray-50 min-h-screen py-12 font-sans">
            <div class="max-w-4xl mx-auto px-4">
                <!-- Actions Bar -->
                <div class="flex justify-between items-center mb-8 no-print">
                    <Link :href="route('home')"
                        class="flex items-center hover:text-gray-900 transition-colors font-bold text-sm tracking-widest">
                        <i class="pi pi-arrow-left mr-2"></i>
                        <span>Back to Home</span>
                    </Link>

                    <div class="flex gap-2">
                        <Button icon="pi pi-copy" severity="secondary" @click="copyLink" v-tooltip.top="'Copy Link'" />
                        <Button icon="pi pi-print" severity="secondary" @click="printPurchaseOrder"
                            v-tooltip.top="'Print Preview'" />
                        <Button icon="pi pi-file-pdf" severity="danger" @click="downloadPDF" :loading="isGenerating"
                            v-tooltip.top="'Download PDF'" />
                        <Button icon="pi pi-image" severity="info" @click="downloadImage" :loading="isGenerating"
                            v-tooltip.top="'Download Image'" />
                    </div>
                </div>

                <!-- Invoice Content -->
                <div ref="invoiceRef"
                    class="printable-area relative bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden print:shadow-none print:border-none print:rounded-none">

                    <!-- Watermark -->
                    <div
                        class="absolute inset-0 flex items-center justify-center pointer-events-none z-[50] overflow-hidden">
                        <span
                            class="text-[8rem] md:text-[8rem] font-black uppercase tracking-widest opacity-[0.4] -rotate-45 whitespace-nowrap transform select-none"
                            :class="{
                                'text-green-600': props.purchaseOrder.payment_status === 'paid',
                                'text-red-600': props.purchaseOrder.payment_status === 'unpaid' || props.purchaseOrder.payment_status === 'failed',
                                'text-amber-600': props.purchaseOrder.payment_status === 'pending',
                                'text-gray-300': !['paid', 'unpaid', 'failed', 'pending'].includes(props.purchaseOrder.payment_status)
                            }">
                            {{ props.purchaseOrder.payment_status }}
                        </span>
                    </div>

                    <div class="overflow-x-auto print:overflow-visible">
                        <table class="relative z-10 w-full md:min-w-0">
                            <!-- HEADER ROW -->
                            <tr>
                                <td colspan="2">
                                    <div
                                        class="px-8 md:px-12 pt-12 pb-2 print:px-12 print:pt-12 print:pb-0 relative overflow-hidden">
                                        <table class="w-full">
                                            <tr>
                                                <td class="align-top">
                                                    <img v-if="page.props.settings?.site_logo"
                                                        :src="`/storage/${page.props.settings.site_logo}`" alt="Logo"
                                                        class="h-14 w-auto">
                                                    <h1 class="text-3xl md:text-4xl pt-2 font-black mb-2 leading-none">
                                                        PURCHASE ORDER</h1>
                                                </td>
                                                <td class="text-right align-top">
                                                    <div class="mb-2">
                                                        <label class="block font-bold mb-1">PO Number</label>
                                                        <p
                                                            class="text-2xl md:text-3xl font-black font-mono text-emerald-400">
                                                            #{{
                                                                props.purchaseOrder.po_number }}</p>
                                                    </div>
                                                    <div>
                                                        <label class="block font-bold mb-1">Date Issued</label>
                                                        <p class="text-sm md:text-base">{{
                                                            formatDateIndonesian(props.purchaseOrder.created_at)
                                                            }}</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>

                            <!-- INFO ROW -->
                            <tr>
                                <td class="px-8 md:px-12 pt-0 print:px-12 print:pt-0 print:pb-5 align-top w-1/2">
                                    <h3 class="mb-2 font-bold">Supplier Details</h3>
                                    <table class="mb-2">
                                        <tr>
                                            <td class="w-14 pr-4">
                                                <div
                                                    class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                                    <i class="pi pi-building text-2xl"></i>
                                                </div>
                                            </td>
                                            <td>
                                                <p
                                                    class="text-base md:text-lg font-black text-gray-900 capitalize leading-none mb-1">
                                                    {{
                                                        props.purchaseOrder.supplier?.name }}</p>
                                                <p class="text-sm text-gray-500">{{
                                                    props.purchaseOrder.supplier?.address }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="">
                                        <label class="block font-bold mb-1">Supplier Contact</label>
                                        <p class="text-sm leading-relaxed text-gray-600">{{
                                            props.purchaseOrder.supplier?.phone }}</p>
                                    </div>
                                </td>
                                <td
                                    class="px-8 md:px-12 pt-0 print:px-12 print:pt-0 print:pb-5 align-top text-right w-1/2">
                                    <div class="mb-2">
                                        <label class="block font-bold mb-2">PO Status</label>
                                        <div>
                                            <span class="inline-block px-4 py-1.5 rounded-full" :class="{
                                                'bg-green-100 text-green-700': props.purchaseOrder.status === 'completed',
                                                'bg-blue-100 text-blue-700': props.purchaseOrder.status === 'processing',
                                                'bg-yellow-100 text-yellow-700': props.purchaseOrder.status === 'pending',
                                                'bg-red-100 text-red-700': props.purchaseOrder.status === 'cancelled',
                                            }">
                                                {{ props.purchaseOrder.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-bold mb-2">Financial Account</label>
                                        <p class="font-black">
                                            {{ props.purchaseOrder.financial_account?.name }}
                                            <br>
                                            <span class="text-gray-500">{{
                                                props.purchaseOrder.financial_account?.account_number }}</span>
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <!-- ITEMS ROW -->
                            <tr>
                                <td colspan="2" class="px-6 md:px-8 pb-2 print:px-8 print:pb-2">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-emerald-50 print:bg-emerald-50">
                                                <th
                                                    class="text-left py-3 font-black pl-2 border-b border-gray-100 text-sm md:text-base">
                                                    No</th>
                                                <th
                                                    class="text-left py-3 font-black border-b border-gray-100 text-sm md:text-base">
                                                    Item Details
                                                </th>
                                                <th
                                                    class="text-center py-3 font-black border-b border-gray-100 text-sm md:text-base">
                                                    Qty</th>
                                                <th
                                                    class="text-right py-3 font-black border-b border-gray-100 text-sm md:text-base">
                                                    Unit Cost
                                                </th>
                                                <th
                                                    class="text-right py-3 font-black pr-2 border-b border-gray-100 text-sm md:text-base">
                                                    Amount
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in props.purchaseOrder.purchase_order_items"
                                                :key="item.id" class="border-b border-gray-100 print:border-gray-100">
                                                <td class="py-2 pl-2 text-sm">{{ index + 1 }}</td>
                                                <td class="py-2">
                                                    <div class="font-black text-sm">{{ item.product?.name }}</div>
                                                    <div class="text-xs text-gray-500">{{ item.product?.category?.name
                                                        }}
                                                    </div>
                                                </td>
                                                <td class="py-2 text-center text-sm font-black">{{ item.quantity }}</td>
                                                <td class="py-2 text-right text-sm">{{
                                                    formatCurrencyIndo(item.cost_price) }}</td>
                                                <td class="py-2 text-right text-sm font-black pr-2">
                                                    {{ formatCurrencyIndo(item.cost_price * item.quantity) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <!-- FOOTER ROW -->
                            <tr>
                                <td
                                    class="ps-8 pt-6 pb-12 print:ps-8 print:pt-6 print:pb-12 footer-row align-top border-t border-gray-100 print:bg-white w-1/2">
                                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 w-full max-w-sm">
                                        <h4 class="font-black text-sm mb-2">Notes & Remarks</h4>
                                        <p class="text-sm break-words whitespace-pre-wrap text-gray-600">{{
                                            props.purchaseOrder.notes || '-' }}</p>
                                    </div>
                                </td>
                                <td
                                    class="pr-8 pt-6 pb-12 print:pr-8 print:pt-6 print:pb-12 footer-row align-top border-t border-gray-100 print:bg-white w-1/2">
                                    <table class="w-full max-w-xs ml-auto border-collapse">
                                        <tr class="text-gray-600">
                                            <td class="text-left py-1 font-bold text-sm md:text-base">Subtotal</td>
                                            <td class="text-right py-1 font-bold text-sm md:text-base">
                                                {{ formatCurrencyIndo(props.purchaseOrder.grand_total -
                                                    props.purchaseOrder.shipping_cost) }}
                                            </td>
                                        </tr>
                                        <tr class="text-gray-600">
                                            <td class="text-left py-1 font-bold text-sm md:text-base">Shipping Cost</td>
                                            <td class="text-right py-1 font-bold text-sm md:text-base">
                                                {{ formatCurrencyIndo(props.purchaseOrder.shipping_cost) }}
                                            </td>
                                        </tr>
                                        <tr class="text-emerald-600">
                                            <td class="text-left pt-3 font-black text-base md:text-lg">Grand Total</td>
                                            <td class="text-right pt-3 text-2xl md:text-3xl font-black leading-none">
                                                {{ formatCurrencyIndo(props.purchaseOrder.grand_total) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="print-copyright hidden print:block text-center py-8 font-bold text-xs text-gray-400">
                        Copyright &copy; {{ new Date().getFullYear() }} {{ $page.props.settings?.site_name }} - All
                        Rights Reserved
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center min-h-screen">
            <div class="animate-pulse text-gray-400">Loading Purchase Order...</div>
        </div>
    </FrontLayout>
</template>

<style scoped>
@media print {
    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        background: white;
        visibility: hidden;
    }

    .printable-area {
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: white;
        z-index: 9999;
    }

    .printable-area * {
        visibility: visible;
    }

    .no-print {
        display: none !important;
    }

    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>