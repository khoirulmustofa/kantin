<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatCurrencyIndo, formatDateIndonesian } from '@/Utils/formatter';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import FrontLayout from '@/Layouts/FrontLayout.vue';

const props = defineProps({
    order: Object,
    menu: String,
    title: String,
});

const page = usePage();

const invoiceRef = ref(null);
const isGenerating = ref(false);

const printInvoice = () => {
    window.print();
};

const downloadPDF = async () => {
    if (!invoiceRef.value) return;
    isGenerating.value = true;

    // Save current scroll position
    const scrollX = window.scrollX;
    const scrollY = window.scrollY;
    window.scrollTo(0, 0);

    try {
        const canvas = await html2canvas(invoiceRef.value, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            windowWidth: 1200, // Force consistent width for capture
            onclone: (clonedDoc) => {
                // Force light mode on the cloned document for consistent PDF output
                const element = clonedDoc.querySelector('html');
                if (element) {
                    element.classList.remove('my-app-dark');
                }
                // Ensure all fonts are loaded in the clone
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

        const margin = 10; // 10mm margin
        const pdfWidth = pdf.internal.pageSize.getWidth() - (margin * 2);
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

        pdf.addImage(imgData, 'PNG', margin, margin, pdfWidth, pdfHeight, undefined, 'FAST');
        pdf.save(`Invoice-${props.order.order_number}.pdf`);
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
        link.download = `Invoice-${props.order.order_number}.png`;
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
        <div class="bg-gray-50 min-h-screen py-12 font-sans">
            <div class="max-w-4xl mx-auto px-4">
                <!-- Actions Bar -->
                <div class="flex justify-between items-center mb-8 no-print">
                    <Link :href="route('home')"
                        class="flex items-center  hover:text-gray-900 transition-colors font-bold text-sm  tracking-widest">
                        <i class="pi pi-arrow-left mr-2"></i>
                        <span>Back to Home</span>
                    </Link>

                    <div class="flex gap-2">
                        <Button icon="pi pi-copy" severity="secondary" @click="copyLink" v-tooltip.top="'Copy Link'" />
                        <Button icon="pi pi-print" severity="secondary" @click="printInvoice"
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
                        class="absolute inset-0 flex items-center justify-center pointer-events-none z-50 overflow-hidden">
                        <span
                            class="text-[8rem] md:text-[8rem] font-black uppercase tracking-widest opacity-[0.4] -rotate-45 whitespace-nowrap transform select-none"
                            :class="{
                                'text-green-600': props.order.payment_status === 'paid',
                                'text-red-600': props.order.payment_status === 'unpaid' || props.order.payment_status === 'failed',
                                'text-amber-600': props.order.payment_status === 'failed',
                                'text-gray-300': !['paid', 'unpaid', 'failed', 'pending'].includes(props.order.payment_status)
                            }">
                            {{ props.order.payment_status }}
                        </span>
                    </div>

                    <div class="overflow-x-auto print:overflow-visible">
                        <table class="relative z-10 w-full md:min-w-0">
                            <tbody>
                                <!-- HEADER ROW -->
                                <tr>
                                    <td colspan="2">
                                        <div
                                            class="px-12 pt-12 pb-2 print:px-12 print:pt-12 print:pb-0 relative overflow-hidden">
                                            <table class="w-full">
                                                <tbody>
                                                    <tr>
                                                        <td class="align-top">
                                                            <img v-if="page.props.settings.site_logo"
                                                                :src="`/storage/${page.props.settings.site_logo}`"
                                                                alt="Logo" class="h-14 w-auto ">
                                                            <h1
                                                                class="text-4xl pt-2 font-bold font-black er mb-2 leading-none">
                                                                INVOICE</h1>

                                                        </td>
                                                        <td class="text-right align-top">
                                                            <div class="mb-2">
                                                                <label class="block  font-bold mb-1">Order
                                                                    Number</label>
                                                                <p
                                                                    class="text-3xl font-black font-mono text-emerald-400">
                                                                    #{{
                                                                        props.order.order_number }}</p>
                                                            </div>
                                                            <div>
                                                                <label class="block  font-bold mb-1">Date
                                                                    Issued</label>
                                                                <p class="">{{
                                                                    formatDateIndonesian(props.order.created_at) }}</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>

                                <!-- INFO ROW -->
                                <tr>
                                    <td class="px-12 pt-0 print:px-12 print:pt-0 print:pb-5 align-top w-1/2">
                                        <h3 class="mb-2 font-bold">
                                            Customer Details</h3>
                                        <table class="mb-2">
                                            <tbody>
                                                <tr>
                                                    <td class="w-14 pr-4">
                                                        <div
                                                            class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center  border border-gray-100">
                                                            <i class="pi pi-user text-2xl"></i>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p
                                                            class="text-lg font-black text-gray-900 capitalize leading-none mb-1">
                                                            {{
                                                                props.order.user?.name }}</p>
                                                        <p class="text-sm ">{{ props.order.user?.username }}</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="">
                                            <label class="block font-bold  mb-1">Shipping
                                                Address</label>
                                            <p class="text-sm  leading-relaxed">{{
                                                props.order.shipping_address }}</p>
                                        </div>
                                    </td>
                                    <td class="px-12 pt-0 print:px-12 print:pt-0 print:pb-5 align-top text-right w-1/2">
                                        <div class="mb-2">
                                            <label class="block font-bold  mb-2">Order
                                                Status</label>
                                            <div>
                                                <span class="inline-block px-4 py-1.5 rounded-full  " :class="{
                                                    'bg-green-100 text-green-700': props.order.status === 'completed',
                                                    'bg-blue-100 text-blue-700': props.order.status === 'processing',
                                                    'bg-yellow-100 text-yellow-700': props.order.status === 'pending',
                                                    'bg-red-100 text-red-700': props.order.status === 'cancelled',
                                                }">
                                                    {{ props.order.status }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-bold  mb-2">Payment Method</label>
                                            <p class="font-black">
                                                {{ props.order.financial_account?.name }}
                                                <br>
                                                <span class="text-gray-500">{{
                                                    props.order.financial_account?.account_number
                                                    }}</span>
                                            </p>
                                        </div>
                                    </td>
                                </tr>

                                <!-- ITEMS ROW -->
                                <tr>
                                    <td colspan="2" class="px-8 pb-2  print:px-8 print:pb-2">
                                        <table class="w-full border-collapse">
                                            <thead>
                                                <tr class="bg-green-100 print:bg-green-100">
                                                    <th
                                                        class="text-left pb-2 pt-5 font-black pl-2 border-b border-gray-100">
                                                        No</th>
                                                    <th
                                                        class="text-left pb-2 pt-5 font-black  border-b border-gray-100">
                                                        Item Details</th>
                                                    <th
                                                        class="text-center pb-2 pt-5 font-black  border-b border-gray-100">
                                                        Qty</th>
                                                    <th
                                                        class="text-right pb-2 pt-5 font-black border-b border-gray-100">
                                                        Unit Price</th>
                                                    <th
                                                        class="text-right pb-2 pt-5 font-black pr-2 border-b border-gray-100">
                                                        Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in props.order.order_items" :key="item.id"
                                                    class="border-b border-gray-300 print:border-gray-300">
                                                    <td class="py-1 pl-2">
                                                        {{ index + 1 }}
                                                    </td>
                                                    <td class="py-1 ">
                                                        <table class="w-auto">
                                                            <tbody>
                                                                <tr>

                                                                    <td>
                                                                        <p class=" font-black   mb-1">
                                                                            {{ item.product?.name }}</p>
                                                                        <span
                                                                            class="inline-block font-black  text-gray-500 text-sm ">{{
                                                                                item.product?.category?.name || 'Product'
                                                                            }}</span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class="py-1 text-center text-sm font-black text-gray-900">
                                                        {{ item.quantity }}</td>
                                                    <td class="py-1 text-right text-sm font-bold ">
                                                        {{ formatCurrencyIndo(item.selling_price) }}</td>
                                                    <td class="py-1 text-right text-sm font-black text-gray-900 pr-2">
                                                        {{ formatCurrencyIndo(item.selling_price * item.quantity) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <!-- FOOTER ROW -->
                                <tr>
                                    <td
                                        class="ps-8 pt-3 pb-10 print:ps-8 print:pt-3 print:pb-10 footer-row align-bottom  border-t border-gray-100 print:bg-white w-1/2">
                                        <div class="bg-white p-4 rounded-xl border border-gray-200 w-full max-w-sm">
                                            <h4 class=" font-black  ">
                                                Notes & Remarks</h4>
                                            <p class="font-sm wrap-break-word">* Silahkan kirim <span
                                                    class="font-bold">bukti transfer</span> Anda melalui WhatsApp untuk
                                                mempercepat proses verifikasi pesanan</p>
                                            <p class="font-bold">WhatsApp: {{ $page.props.settings?.whatsapp }}</p>
                                        </div>
                                    </td>
                                    <td
                                        class="pr-8 pt-3 pb-10 print:pr-8 print:pt-3 print:pb-10 footer-row align-bottom  border-t border-gray-100 print:bg-white w-1/2">
                                        <table class="w-full max-w-xs ml-auto border-collapse">
                                            <tbody>
                                                <tr>
                                                    <td class="text-left py-2 font-bold ">
                                                        Subtotal</td>
                                                    <td class="text-right py-2 font-bold text-xl">{{
                                                        formatCurrencyIndo(props.order.grand_total -
                                                            props.order.shipping_cost)
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left py-2 pb-2 font-bold  border-b border-gray-900">
                                                        Shipping Fee</td>
                                                    <td
                                                        class="text-right py-2 font-bold text-xl border-b border-gray-900">
                                                        {{ formatCurrencyIndo(props.order.shipping_cost) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-left pt-4 font-bold  text-emerald-500">
                                                        Grand Total</td>
                                                    <td
                                                        class="text-right pt-4 text-4xl font-black text-emerald-600 er leading-none">
                                                        {{ formatCurrencyIndo(props.order.grand_total) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="print-copyright hidden print:block text-center mt-8  font-bold  ">
                        Copyright &copy; {{ new Date().getFullYear() }} {{ $page.props.settings.site_name }} - All
                        Rights
                        Reserved
                    </div>
                </div>
            </div>
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

    /* Force tables to behave */
    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>