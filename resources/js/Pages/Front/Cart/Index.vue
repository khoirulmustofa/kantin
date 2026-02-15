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
        message: 'Are you sure you want to clear all items from your cart?',
        header: 'Clear Cart Confirmation',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            cartStore.clearCart();
        }
    });
};
</script>


<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-10 tracking-tighter">{{ title }}</h1>

            <div v-if="cartStore.items.length > 0" class="flex flex-col lg:flex-row gap-12">
                <!-- Cart Items -->
                <div class="lg:w-2/3 space-y-2">
                    <div v-for="item in cartStore.items" :key="item.id"
                        v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 slide-in-from-l-8 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"
                        class="flex flex-col sm:flex-row items-center justify-between p-2 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:shadow-gray-100/50 transition-all duration-500">


                        <div class="flex items-center gap-8 w-full sm:w-auto mb-6 sm:mb-0">
                            <!-- Image -->
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-50 border border-gray-100">
                                <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                    class="w-full h-full object-cover">
                                <img v-else src="\assets\images\placeholder.webp" :alt="item.name"
                                    class="w-full h-full object-cover">
                            </div>

                            <!-- Info -->
                            <div>
                                <Link :href="route('product.show', item.slug)">
                                    <h3
                                        class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight hover:text-rose-600 transition-colors">
                                        {{ item.name }}</h3>
                                </Link>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{
                                    item.category }}</p>
                                <span class="text-green-800 font-black">{{ formatCurrencyIndo(item.price) }}</span>
                            </div>
                        </div>

                        <!-- Quantity & Total -->
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-gray-400 mb-2">Quantity</span>
                                <InputNumber v-model="item.quantity"
                                    @update:modelValue="(val) => cartStore.updateQuantity(item.id, val)" showButtons
                                    buttonLayout="horizontal" :min="1"
                                    class="!rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700"
                                    incrementButtonClass="!bg-gray-50 dark:!bg-gray-800 !text-gray-600 !border-none"
                                    decrementButtonClass="!bg-gray-50 dark:!bg-gray-800 !text-gray-600 !border-none"
                                    inputClass="!w-12 !text-center !font-black !border-none !text-xs" />
                            </div>

                            <div class="text-right min-w-[120px]">
                                <span
                                    class="text-sm font-black  tracking-widest text-gray-400 mb-2 block">Subtotal</span>
                                <span class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">{{
                                    formatCurrencyIndo(item.price * item.quantity) }}</span>
                            </div>

                            <button @click="cartStore.removeItem(item.id)"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-300 hover:text-rose-600 hover:bg-rose-50 transition-all">
                                <i class="pi pi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Cart Action -->
                    <div class="flex justify-end pt-4">
                        <Button @click="confirmClearCart()" label="Clear All" icon="pi pi-trash" severity="danger" text
                            class="!font-black !text-xs !tracking-widest" />
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:w-1/3">

                    <div v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 zoom-in-50 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"
                        class="sticky top-32 p-5 bg-green-50 border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 text-black">
                        <h2 class="text-2xl font-black tracking-tight mb-8">Order Summary</h2>

                        <div class="space-y-4 mb-10">
                            <div class="flex justify-between items-center opacity-60">
                                <span class="text-sm font-bold  tracking-widest">Subtotal ({{ cartStore.totalItems }}
                                    items)</span>
                                <span class="font-black">{{ formatCurrencyIndo(cartStore.totalPrice) }}</span>
                            </div>

                            <div class="flex justify-between items-center opacity-60">
                                <span class="text-sm font-bold  tracking-widest">Shipping</span>
                                <span class="text-emerald-500 font-black">FREE</span>
                            </div>

                            <div class="pt-8 mt-8 border-t border-gray-100 flex justify-between items-baseline">
                                <span class="text-sm font-black  tracking-[0.2em]">Total</span>
                                <div class="text-right">
                                    <span class="text-3xl font-black tracking-tighter text-green-800">
                                        {{ formatCurrencyIndo(cartStore.totalPrice) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <Link :href="route('checkout.index')"
                                class="block w-full text-center bg-green-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-gray-200 hover:bg-rose-600 hover:-translate-y-1 transition-all active:scale-95">
                                Process to Checkout
                            </Link>

                            <Link :href="route('product.index')"
                                class="block text-center font-bold text-gray-400 hover:text-gray-900 transition-colors">
                                <i class="pi pi-arrow-left mr-2"></i> Continue Shopping
                            </Link>
                        </div>
                    </div>
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
