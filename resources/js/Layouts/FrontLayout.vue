<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';

const cartStore = useCartStore();

// State untuk UI
const isMobileMenuOpen = ref(false);
const isSearchOpen = ref(false);

// State untuk Dropdown Desktop
const activeDropdown = ref(null); // 'men', 'women', atau null

// State untuk Dropdown Mobile
const mobileMenOpen = ref(false);
const mobileWomenOpen = ref(false);

// Fungsi Helper
const closeAll = () => {
    isMobileMenuOpen.value = false;
    activeDropdown.value = null;
    isSearchOpen.value = false;
};

const menuActive = defineModel('menuActive');
const title = defineModel('title');

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <ConfirmDialog />
    <Toast />
    <div class="min-h-screen flex flex-col font-sans text-gray-900">
        <header :class="[
            'sticky top-0 z-50 transition-all duration-500',
            isScrolled
                ? 'bg-blue-900/50 backdrop-blur-xl border-b border-white/10 py-2 shadow-lg'
                : 'bg-blue-900 py-2'
        ]">
            <div class="container mx-auto flex justify-between items-center py-2 px-4 lg:px-0">
                <Link href="/" class="flex items-center">
                    <div>
                        <img src="assets/images/template-white-logo.png" alt="Logo" class="h-14 w-auto mr-4">
                    </div>
                </Link>

                <div class="flex lg:hidden">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-white focus:outline-none">
                        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <nav class="hidden lg:flex md:flex-grow justify-center">
                    <ul class="flex justify-center space-x-2 text-white p-1">
                        <li>
                            <Link href="/"
                                class="px-4 py-2 rounded-xl transition-all duration-300 font-semibold flex items-center"
                                :class="$page.props.menu === 'home' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'hover:bg-white/10 text-white/70 hover:text-white'">
                                Home
                            </Link>
                        </li>

                        <li>
                            <Link href="/product"
                                class="px-4 py-2 rounded-xl transition-all duration-300 font-semibold flex items-center"
                                :class="$page.props.menu === 'products' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'hover:bg-white/10 text-white/70 hover:text-white'">
                                Product
                            </Link>
                        </li>

                        <li>
                            <Link href="/checkout"
                                class="px-4 py-2 rounded-xl transition-all duration-300 font-semibold flex items-center"
                                :class="$page.props.menu === 'checkout' ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' : 'hover:bg-white/10 text-white/70 hover:text-white'">
                                Checkout
                            </Link>
                        </li>
                    </ul>
                </nav>

                <div class="hidden lg:flex items-center space-x-4 relative">
                    <Link href="/register"
                        class="bg-primary border border-primary hover:bg-transparent text-white hover:text-primary font-semibold px-4 py-2 rounded-full transition">
                        Register</Link>
                    <Link href="/login"
                        class="bg-primary border border-primary hover:bg-transparent text-white hover:text-primary font-semibold px-4 py-2 rounded-full transition">
                        Login</Link>

                    <div class="relative group">
                        <Link :href="route('cart.index')" class="relative">
                            <img src="/assets/images/cart-shopping.svg" alt="Cart"
                                class="h-6 w-6 transition group-hover:scale-110 brightness-200">
                            <span v-if="cartStore.totalItems > 0"
                                class="absolute -top-2 -right-2 bg-yellow-300 text-black text-[10px] font-black px-1.5 py-0.5 rounded-full border-2 border-yellow-500 shadow-lg">
                                {{ cartStore.totalItems }}
                            </span>
                        </Link>
                        <!-- Mini Cart Dropdown -->
                        <div
                            class="absolute right-0 mt-1 w-80 bg-white shadow-2xl p-6 rounded-3xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 text-black border-t-4 border-blue-600 z-[60] translate-y-2 group-hover:translate-y-0">
                            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 px-1">My Cart
                            </h3>

                            <div v-if="cartStore.items.length > 0"
                                class="space-y-6 max-h-96 overflow-y-auto pr-2 no-scrollbar">
                                <div v-for="item in cartStore.items" :key="item.id"
                                    class="flex items-center justify-between group/item">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-16 h-16 rounded-2xl overflow-hidden bg-gray-50 border border-gray-100">
                                            <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                                class="w-full h-full object-cover">
                                            <div v-else
                                                class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                                <i class="pi pi-image text-xl"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p
                                                class="font-black text-xs uppercase tracking-tight text-gray-900 line-clamp-1 mb-1">
                                                {{ item.name }}</p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400">Qty: {{ item.quantity
                                                    }}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                                                <span class="text-xs font-black text-blue-600">{{
                                                    formatCurrencyIndo(item.price) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="cartStore.removeItem(item.id)"
                                        class="text-gray-300 hover:text-blue-600 transition-colors opacity-0 group-hover/item:opacity-100">
                                        <i class="pi pi-times text-xs"></i>
                                    </button>
                                </div>

                                <div class="pt-6 border-t border-gray-100">
                                    <div class="flex items-center justify-between mb-6">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total</span>
                                        <span class="text-xl font-black text-gray-900 tracking-tighter">{{
                                            formatCurrencyIndo(cartStore.totalPrice) }}</span>
                                    </div>
                                    <Link :href="route('cart.index')"
                                        class="block text-center bg-gray-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black hover:shadow-xl hover:shadow-gray-200 transition-all active:scale-95">
                                        Go to Checkout</Link>
                                </div>
                            </div>

                            <div v-else class="flex flex-col items-center justify-center py-10 opacity-50 italic">
                                <i class="pi pi-shopping-cart text-4xl mb-4"></i>
                                <p class="text-xs font-bold uppercase tracking-widest text-center">Cart is empty</p>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <transition name="mobile-slide">
                <nav v-if="isMobileMenuOpen"
                    class="mobile-menu flex flex-col items-center space-y-4 lg:hidden bg-gray-dark text-white p-6 pb-10">
                    <ul class="w-full text-center">
                        <li>
                            <Link href="/" class="hover:text-secondary font-bold block py-2">Home</Link>
                        </li>

                        <li>
                            <Link href="/product" class="hover:text-secondary font-bold block py-3">Product</Link>
                        </li>

                        <li>
                            <Link href="/checkout" class="hover:text-secondary font-bold block py-3">Checkout</Link>
                        </li>
                    </ul>

                    <div class="flex flex-col w-full space-y-3 pt-4">
                        <Link href="/register" class="bg-primary text-white py-3 rounded-full font-bold text-center">
                            Register</Link>
                        <Link href="/login"
                            class="bg-transparent border border-white text-white py-3 rounded-full font-bold text-center">
                            Login</Link>
                    </div>
                </nav>
            </transition>
        </header>

        <slot />

        <footer class="bg-white border-t border-gray-200 pt-20">
            <div class="container mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                    <div class="lg:col-span-2">
                        <img src="/assets/images/template-logo.png" alt="Logo" class="h-12 mb-6">
                        <p class="text-gray-500 max-w-sm mb-4">Your shop's description goes here. Elevate your lifestyle
                            with our premium sportswear collection.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="h-8 w-8 transition hover:scale-110"><img
                                    src="/assets/images/social_icons/facebook.svg" alt="FB"></a>
                            <a href="#" class="h-8 w-8 transition hover:scale-110"><img
                                    src="/assets/images/social_icons/instagram.svg" alt="IG"></a>
                            <a href="#" class="h-8 w-8 transition hover:scale-110"><img
                                    src="/assets/images/social_icons/twitter.svg" alt="TW"></a>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Shop</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>
                                <Link href="/shop" class="hover:text-amber-500">Men</Link>
                            </li>
                            <li>
                                <Link href="/shop" class="hover:text-amber-500">Women</Link>
                            </li>
                            <li>
                                <Link href="/shop" class="hover:text-amber-500">Accessories</Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Account</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>
                                <Link href="/login" class="hover:text-amber-500">My Account</Link>
                            </li>
                            <li>
                                <Link :href="route('cart.index')" class="hover:text-amber-500">Cart</Link>
                            </li>
                            <li>
                                <Link href="/checkout" class="hover:text-amber-500">Checkout</Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Support</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>
                                <Link href="/faq" class="hover:text-amber-500">FAQ</Link>
                            </li>
                            <li>
                                <Link href="/privacy" class="hover:text-amber-500">Privacy Policy</Link>
                            </li>
                            <li>
                                <Link href="/contact" class="hover:text-amber-500">Contact Us</Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 py-6 border-t border-gray-100">
                <div
                    class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p>&copy; 2026 Your Company. All rights reserved.</p>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <img src="/assets/images/social_icons/visa.svg" class="h-6">
                        <img src="/assets/images/social_icons/paypal.svg" class="h-6">
                        <img src="/assets/images/social_icons/stripe.svg" class="h-6">
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Animasi Fade untuk Dropdown */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

/* Animasi Slide untuk Search */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

/* Animasi Slide Menu Mobile */
.mobile-slide-enter-active,
.mobile-slide-leave-active {
    transition: max-height 0.4s ease-in-out, opacity 0.3s;
    max-height: 600px;
    overflow: hidden;
}

.mobile-slide-enter-from,
.mobile-slide-leave-to {
    max-height: 0;
    opacity: 0;
}

/* Warna tambahan jika belum didefinisikan di Tailwind */
.bg-gray-dark {
    background-color: #1a1a1a;
}

.text-secondary {
    color: #fbbf24;
}

/* Ganti dengan hex warna secondary Anda */
.bg-primary {
    background-color: #3b82f6;
}

/* Ganti dengan hex warna primary Anda */
.border-primary {
    border-color: #3b82f6;
}
</style>