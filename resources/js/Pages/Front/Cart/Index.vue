<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({
    menu: String,
    title: String,
});

const cartStore = useCartStore();
const confirm = useConfirm();

const confirmClearCart = () => {
    confirm.require({
        message: 'Apakah kamu yakin ingin menghapus semua item dari keranjang?',
        header: 'Konfirmasi Hapus Keranjang',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger',
            icon: 'pi pi-trash'
        },
        accept: () => {
            cartStore.clearCart();
        }
    });
};

const confirmRemoveItem = (item) => {
    confirm.require({
        message: `Apakah kamu yakin ingin menghapus ${item.name} dari keranjang?`,
        header: 'Konfirmasi Hapus Item',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Hapus',
            severity: 'danger',
            icon: 'pi pi-trash'
        },
        accept: () => {
            cartStore.removeItem(item.id)
        }
    });
};
</script>


<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title" :hideBottomNav="true">
        <div class="px-4 py-6 pb-32 min-h-[80vh]">
            <h1 class="text-xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">Keranjang Belanja</h1>

            <div v-if="cartStore.items.length > 0" class="flex flex-col gap-2">
                <!-- Cart Items List -->
                <div class="space-y-2">
                    <div v-for="item in cartStore.items" :key="item.id"
                        class="flex gap-4 p-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">

                        <!-- Thumbnail -->
                        <div
                            class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-100">
                            <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                class="w-full h-full object-cover">
                            <img v-else src="/assets/images/placeholder.webp" :alt="item.name"
                                class="w-full h-full object-cover opacity-50">
                        </div>

                        <!-- Details -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <Link :href="route('product.show', item.slug)">
                                    <h3
                                        class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-tight mb-1">
                                        {{ item.name }}
                                    </h3>
                                </Link>
                                <p class="text-xs text-gray-500 font-medium">{{ item.category }}</p>
                            </div>

                            <div class="flex items-end justify-between mt-2">
                                <span class="text-sm font-black text-green-600">
                                    {{ formatCurrencyIndo(item.price) }}
                                </span>

                                <!-- Mobile Quantity Control -->
                                <div
                                    class="flex items-center bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <button
                                        @click="item.quantity > 1 ? cartStore.updateQuantity(item.id, item.quantity - 1) : confirmRemoveItem(item)"
                                        class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-red-500 transition-colors">
                                        <i class="pi"
                                            :class="item.quantity > 1 ? 'pi-minus text-[10px]' : 'pi-trash text-[10px]'"></i>
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-gray-900 dark:text-white">{{
                                        item.quantity }}</span>
                                    <button @click="cartStore.updateQuantity(item.id, item.quantity + 1)"
                                        class="w-7 h-7 flex items-center justify-center text-green-600 hover:bg-green-100 rounded-r-lg transition-colors">
                                        <i class="pi pi-plus text-[10px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear Cart Button -->
                <button @click="confirmClearCart()"
                    class="w-full py-3 text-xs font-bold text-red-500 bg-red-50 rounded-xl border border-red-100 hover:bg-red-100 transition-colors flex items-center justify-center gap-2">
                    <i class="pi pi-trash"></i> Kosongkan Keranjang
                </button>

                <!-- Order Summary (Bottom Fixed on Mobile) -->
                <div
                    class="fixed bottom-[80px] left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-gray-100 p-4 z-40 pb-safe shadow-[0_-4px_20px_rgba(0,0,0,0.05)] rounded-2xl">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-bold text-gray-500">Total Pembayaran</span>
                        <span class="text-lg font-black text-green-700">{{ formatCurrencyIndo(cartStore.totalPrice)
                        }}</span>
                    </div>

                    <Link :href="route('checkout.index')"
                        class="block w-full text-center bg-green-600 text-white py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-green-200 active:scale-95 transition-transform">
                        Checkout Sekarang ({{ cartStore.totalItems }})
                    </Link>
                </div>
            </div>

            <!-- Empty Cart State -->
            <div v-else class="relative overflow-hidden">
                <div
                    class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-96 bg-indigo-50/50 dark:bg-indigo-900/10 blur-[100px] rounded-full">
                </div>

                <div
                    class="relative flex flex-col items-center justify-center py-10 bg-white dark:bg-gray-900/40 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm">

                    <div class="relative mb-10 animate-bounce duration-[3000ms]">
                        <div
                            class="w-40 h-40 bg-gradient-to-tr from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 rounded-full flex items-center justify-center shadow-2xl shadow-gray-200/50 dark:shadow-none border border-white dark:border-gray-700">
                            <i class="pi pi-shopping-cart !text-6xl text-indigo-500/30"></i>
                        </div>
                    </div>

                    <div class="text-center max-w-sm px-6">
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-3 tracking-tighter">
                            Keranjang Kosong
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 font-medium leading-relaxed mb-10">
                            Sepertinya Anda belum memilih seragam atau kebutuhan santri lainnya. Mari mulai belanja
                            sekarang!
                        </p>
                    </div>

                    <Link :href="route('product.index')"
                        class="group relative inline-flex items-center gap-3 bg-green-600 text-white px-10 py-5 rounded-2xl font-black text-xs tracking-[0.2em] uppercase transition-all hover:scale-105 active:scale-95 shadow-xl shadow-gray-200 dark:shadow-indigo-900/20">
                        <i class="pi pi-arrow-left text-[10px] transition-transform group-hover:-translate-x-1"></i>
                        Jelajahi Produk
                    </Link>
                </div>
            </div>
        </div>
    </FrontLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
