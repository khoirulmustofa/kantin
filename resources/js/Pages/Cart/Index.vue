<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';

// PrimeVue Components
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    menu: String,
    title: String,
});

const cartStore = useCartStore();

</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-10 tracking-tighter uppercase">Your
                Treasures</h1>

            <div v-if="cartStore.items.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-6">
                    <div v-for="item in cartStore.items" :key="item.id"
                        class="flex flex-col sm:flex-row items-center justify-between p-8 bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 hover:shadow-2xl hover:shadow-gray-100 transition-all duration-500">

                        <div class="flex items-center gap-8 w-full sm:w-auto mb-6 sm:mb-0">
                            <!-- Image -->
                            <div class="w-24 h-24 rounded-3xl overflow-hidden bg-gray-50 border border-gray-100">
                                <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                    class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="pi pi-image text-3xl"></i>
                                </div>
                            </div>

                            <!-- Info -->
                            <div>
                                <Link :href="route('front.produk.show', item.slug)">
                                    <h3
                                        class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight hover:text-rose-600 transition-colors">
                                        {{ item.name }}</h3>
                                </Link>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{
                                    item.category }}</p>
                                <span class="text-rose-600 font-black">{{ formatCurrencyIndo(item.price) }}</span>
                            </div>
                        </div>

                        <!-- Quantity & Total -->
                        <div class="flex items-center gap-8 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="flex flex-col items-center">
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Quantity</span>
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
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block">Subtotal</span>
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
                        <Button @click="cartStore.clearCart()" label="Clear All" icon="pi pi-trash" severity="danger"
                            text class="!font-black !text-[10px] !uppercase !tracking-widest" />
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-32 p-10 bg-gray-900 rounded-[3rem] text-white shadow-2xl shadow-gray-200">
                        <h2 class="text-2xl font-black uppercase tracking-tighter mb-8 pb-4 border-b border-white/10">
                            Order Summary</h2>

                        <div class="space-y-6 mb-10">
                            <div class="flex justify-between items-center text-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest">Subtotal ({{
                                    cartStore.totalItems }} items)</span>
                                <span class="font-bold">{{ formatCurrencyIndo(cartStore.totalPrice) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest">Shipping</span>
                                <span class="text-emerald-400 font-black">FREE</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-400">
                                <span class="text-[10px] font-black uppercase tracking-widest">Tax (PPN 11%)</span>
                                <span class="text-white font-bold">{{ formatCurrencyIndo(cartStore.totalPrice * 0.11)
                                    }}</span>
                            </div>

                            <div class="pt-6 border-t border-white/10 flex justify-between items-center">
                                <span class="text-lg font-black uppercase tracking-tighter">Total Price</span>
                                <span class="text-3xl font-black text-rose-500 tracking-tighter">{{
                                    formatCurrencyIndo(cartStore.totalPrice * 1.11) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <Link :href="route('admin.orders.create')"
                                class="block w-full text-center bg-white text-gray-900 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-rose-600 hover:text-white transition-all shadow-xl shadow-black/20 active:scale-95">
                                Process to Checkout
                            </Link>
                            <Link :href="route('front.produk.index')"
                                class="block text-center text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-white transition-colors">
                                <i class="pi pi-arrow-left mr-2"></i> Continue Shopping
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Cart State -->
            <div v-else
                class="flex flex-col items-center justify-center py-32 bg-gray-50 dark:bg-gray-900/50 rounded-[3rem] border border-dashed border-gray-200 dark:border-gray-800">
                <div
                    class="w-32 h-32 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-8 shadow-xl">
                    <i class="pi pi-shopping-bag text-5xl text-gray-200"></i>
                </div>
                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4 uppercase tracking-tighter">Your cart
                    is feeling light</h2>
                <p class="text-gray-500 font-bold uppercase tracking-widest mb-10 text-center max-w-sm leading-loose">
                    Looks like you haven't discovered any treasures yet. Start exploring our collections!</p>
                <Link :href="route('front.produk.index')"
                    class="bg-gray-900 text-white px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-gray-200">
                    Explore Products
                </Link>
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
