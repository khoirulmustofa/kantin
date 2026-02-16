<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
    window.open(route('order.print', props.order.id), '_blank');
};

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href).then(() => {
        page.props.flash.success = 'Link copied to clipboard';
        setTimeout(() => {
            page.props.flash.success = null;
        }, 500);
    });
};


const whatsappUrl = computed(() => {
    const phone = page.props.settings?.whatsapp_number || '';
    const name = page.props.settings?.site_name || 'Admin';
    const orderNumber = props.order.order_number;

    // Gunakan \n untuk enter
    const message = encodeURIComponent(
        `Assalamualaikum ${name},\n\n Saya sudah melakukan pembayaran untuk pesanan nomor ${orderNumber}. Berikut adalah bukti transfernya.`
    );

    return `https://wa.me/${phone}?text=${message}`;
});

</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="bg-gray-50 min-h-screen py-6 font-sans">
            <div class="max-w-2xl mx-auto px-4">

                <!-- Navbar / Actions -->
                <div class="flex justify-between items-center mb-6 print:hidden">
                    <Link :href="route('home')"
                        class="flex items-center text-gray-600 hover:text-gray-900 transition-colors font-bold text-sm">
                        <i class="pi pi-arrow-left mr-2"></i>
                        <span>Kembali</span>
                    </Link>

                    <div class="flex gap-2">
                        <div class="relative group">
                            <button
                                class="w-9 h-9 flex items-center justify-center bg-white rounded-full shadow-sm border border-gray-100 text-gray-600 hover:text-green-600 hover:border-green-200 transition-all active:scale-95">
                                <i class="pi pi-download"></i>
                            </button>
                            <div
                                class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 p-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 transform origin-top-right">
                                <button @click="printInvoice"
                                    class="w-full text-left px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg flex items-center gap-2">
                                    <i class="pi pi-print"></i> Print
                                </button>
                                
                                <button @click="copyLink"
                                    class="w-full text-left px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 rounded-lg flex items-center gap-2">
                                    <i class="pi pi-copy"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================== -->
                <!-- MOBILE / SCREEN VIEW       -->
                <!-- ========================== -->
                <div class="space-y-4 print:hidden">

                    <!-- Status Card -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold tracking-widest uppercase mb-1">Order ID</p>
                                <h2 class="text-xl font-black text-gray-900 tracking-tight">#{{ props.order.order_number
                                }}</h2>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide" :class="{
                                'bg-green-100 text-green-700': props.order.payment_status === 'paid',
                                'bg-red-100 text-red-700': props.order.payment_status === 'unpaid' || props.order.payment_status === 'failed',
                                'bg-amber-100 text-amber-700': props.order.payment_status === 'failed',
                                'bg-gray-100 text-gray-600': !['paid', 'unpaid', 'failed'].includes(props.order.payment_status)
                            }">
                                {{ props.order.payment_status }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-t border-gray-50 pt-4">
                            <span class="text-gray-500 font-medium">Tanggal Pesanan</span>
                            <span class="font-bold text-gray-900">{{ formatDateIndonesian(props.order.created_at)
                            }}</span>
                        </div>
                        <div v-if="props.order.payment_status === 'unpaid'"
                            class="mt-4 bg-amber-50 border border-amber-100 text-amber-800 p-3 rounded-xl text-xs flex gap-3 items-start">
                            <i class="pi pi-info-circle text-lg mt-0.5"></i>
                            <div>
                                <p class="font-bold mb-1">Menunggu Pembayaran</p>
                                <p>Silahkan selesaikan pembayaran dan kirim bukti transfer agar pesanan diproses.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900">Item Pesanan</h3>
                            <span class="text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{
                                props.order.order_items.length }} Item</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            <div v-for="item in props.order.order_items" :key="item.id" class="p-4 flex gap-4">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-xl flex-shrink-0 overflow-hidden border border-gray-100">
                                    <img v-if="item.product?.image" :src="`/storage/${item.product.image}`"
                                        class="w-full h-full object-cover">
                                    <img v-else src="/assets/images/placeholder.webp"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 text-sm line-clamp-2 mb-1">{{ item.product?.name
                                    }}</h4>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider mb-2">{{
                                        item.product?.category?.name }}</p>
                                    <div class="flex justify-between items-end">
                                        <p class="text-xs font-medium text-gray-500">{{ item.quantity }} x {{
                                            formatCurrencyIndo(item.selling_price) }}</p>
                                        <p class="text-sm font-black text-gray-900">{{ formatCurrencyIndo(item.price) }}
                                        </p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Shipping -->
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="pi pi-map-marker text-xs"></i>
                                </div>
                                <h3 class="font-bold text-sm text-gray-900">Alamat Pengiriman</h3>
                            </div>
                            <p class="text-xs text-gray-600 font-medium leading-relaxed">{{ props.order.address ||
                                props.order.shipping_address }}</p>
                        </div>

                        <!-- Payment -->
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-2 mb-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                                    <i class="pi pi-wallet text-xs"></i>
                                </div>
                                <h3 class="font-bold text-sm text-gray-900">Metode Pembayaran</h3>
                            </div>
                            <div>
                                <p class="font-bold text-sm text-gray-900">{{ props.order.financial_account?.name }}</p>
                                <p class="text-xs text-gray-500 font-mono mt-1">{{
                                    props.order.financial_account?.account_number }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Bottom -->
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900">{{ formatCurrencyIndo(props.order.grand_total -
                                    props.order.shipping_cost) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Biaya Pengiriman</span>
                                <span class="font-bold text-green-600">GRATIS</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                            <span class="font-bold text-gray-900">Total Tagihan</span>
                            <span class="text-2xl font-black text-green-600 tracking-tight">{{
                                formatCurrencyIndo(props.order.grand_total) }}</span>
                        </div>

                        <!-- WhatsApp Button -->
                        <a :href="whatsappUrl" target="_blank"
                            class="mt-4 block w-full bg-green-500 hover:bg-green-600 text-white font-bold text-center py-3.5 rounded-xl shadow-lg shadow-green-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="pi pi-whatsapp text-lg"></i>
                            Konfirmasi Pembayaran
                        </a>
                    </div>

                </div>

                <!-- ========================== -->
                <!-- INVOICE / PRINT VIEW       -->
                <!-- (Hidden on Screen)         -->
                <!-- ========================== -->
                <div ref="invoiceRef"
                    class="print-container opacity-0 pointer-events-none fixed left-0 top-0 w-[1125px] z-[-50] print:static print:w-full print:opacity-100 print:pointer-events-auto print:z-auto print:block bg-white text-black p-10 font-sans">
                    <!-- Watermark Printing -->
                    <div
                        class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 overflow-hidden">
                        <span
                            class="text-[10rem] font-black uppercase tracking-widest opacity-[0.05] -rotate-45 whitespace-nowrap transform select-none">
                            {{ props.order.payment_status }}
                        </span>
                    </div>

                    <!-- Header -->
                    <div class="flex justify-between items-start mb-12 relative z-10">
                        <div class="flex items-center gap-4">
                            <img v-if="page.props.settings.site_logo" :src="`/storage/${page.props.settings.site_logo}`"
                                alt="Logo" class="h-20 w-auto">
                            <div>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tight leading-none mb-1">INVOICE
                                </h1>
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">{{
                                    $page.props.settings.site_name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Order Number</p>
                            <p class="text-3xl font-black text-green-600 font-mono tracking-tight mb-4">#{{
                                props.order.order_number }}</p>
                            <p class="text-sm font-bold text-gray-600">{{ formatDateIndonesian(props.order.created_at)
                            }}</p>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="flex gap-12 mb-12 relative z-10 border-t border-b border-gray-100 py-8">
                        <div class="w-1/2">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Ditagihkan Kepada
                            </h3>
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                                    <i class="pi pi-user text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-gray-900 mb-1">{{ props.order.user?.name }}</p>
                                    <p class="text-sm text-gray-600 mb-2 font-medium">{{ props.order.user?.username }}
                                    </p>
                                    <p class="text-sm text-gray-500 leading-relaxed max-w-xs">{{
                                        props.order.shipping_address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status &
                                Pembayaran</h3>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 mb-1">Status Pesanan</p>
                                    <span
                                        class="inline-block px-3 py-1 rounded-md text-xs font-black uppercase tracking-wide border"
                                        :class="{
                                            'bg-green-50 text-green-700 border-green-100': props.order.status === 'completed',
                                            'bg-blue-50 text-blue-700 border-blue-100': props.order.status === 'processing',
                                            'bg-yellow-50 text-yellow-700 border-yellow-100': props.order.status === 'pending',
                                            'bg-red-50 text-red-700 border-red-100': props.order.status === 'cancelled',
                                        }">
                                        {{ props.order.status }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-500 mb-1">Status Pembayaran</p>
                                    <span class="font-black" :class="{
                                        'text-green-600': props.order.payment_status === 'paid',
                                        'text-red-600': props.order.payment_status === 'unpaid'
                                    }">{{ props.order.payment_status }}</span>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-bold text-gray-500 mb-1">Metode Pembayaran</p>
                                    <p class="font-bold text-gray-900">{{ props.order.financial_account?.name }} <span
                                            class="text-gray-400 font-normal">({{
                                                props.order.financial_account?.account_number }})</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-12 relative z-10">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-100">
                                    <th
                                        class="text-left font-black text-xs text-gray-400 uppercase tracking-widest pb-4 pl-2">
                                        Item Deskripsi</th>
                                    <th
                                        class="text-center font-black text-xs text-gray-400 uppercase tracking-widest pb-4">
                                        Qty</th>
                                    <th
                                        class="text-right font-black text-xs text-gray-400 uppercase tracking-widest pb-4">
                                        Harga Satuan</th>
                                    <th
                                        class="text-right font-black text-xs text-gray-400 uppercase tracking-widest pb-4 pr-2">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="(item, index) in props.order.order_items" :key="item.id">
                                    <td class="py-4 pl-2">
                                        <p class="font-bold text-gray-900">{{ item.product?.name }}</p>
                                        <p class="text-xs text-gray-500">{{ item.product?.category?.name }}</p>
                                    </td>
                                    <td class="py-4 text-center font-bold text-gray-600">{{ item.quantity }}</td>
                                    <td class="py-4 text-right font-bold text-gray-600">{{
                                        formatCurrencyIndo(item.selling_price) }}</td>
                                    <td class="py-4 text-right font-black text-gray-900 pr-2">{{
                                        formatCurrencyIndo(item.selling_price * item.quantity) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer / Totals -->
                    <div class="flex justify-end relative z-10">
                        <div class="w-1/3 space-y-3">
                            <div class="flex justify-between text-gray-500 text-sm font-bold">
                                <span>Subtotal</span>
                                <span>{{ formatCurrencyIndo(props.order.grand_total - props.order.shipping_cost)
                                }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500 text-sm font-bold">
                                <span>Shipping Fee</span>
                                <span>{{ formatCurrencyIndo(props.order.shipping_cost) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t-2 border-gray-900">
                                <span class="font-black text-gray-900">Grand Total</span>
                                <span class="text-3xl font-black text-green-600 tracking-tight">{{
                                    formatCurrencyIndo(props.order.grand_total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div class="mt-16 pt-8 border-t border-gray-100 flex justify-between items-end relative z-10">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Notes</p>
                            <p class="text-sm text-gray-500 max-w-sm">Terima kasih telah berbelanja di Kantin NFBS
                                Bogor. Bukti pembayaran ini sah dan diproses secara komputerisasi.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-300 uppercase tracking-widest">Kantin NFBS Bogor</p>
                            <p class="text-[10px] text-gray-300 mt-1">{{ new Date().getFullYear() }} All Rights Reserved
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </FrontLayout>
</template>

<style scoped>
@media print {
    @page {
        size: A4 portrait;
        margin: 20mm;
    }

    body * {
        visibility: hidden;
    }

    .print-container,
    .print-container * {
        visibility: visible;
    }

    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 1024px !important;
        width: 100%;
        background: white;
    }

    tr {
        page-break-inside: avoid;
    }




    /* Memastikan warna background (seperti hijau) ikut tercetak */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

}
</style>