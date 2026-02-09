<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

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
</script>

<template>
    <div class="min-h-screen flex flex-col font-sans text-gray-900">
        <header class="bg-blue-900 sticky top-0 z-50">
            <div class="container mx-auto flex justify-between items-center py-4 px-4 lg:px-0">
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
                    <ul class="flex justify-center space-x-4 text-white">
                        <li>
                            <Link href="/" class="hover:text-secondary font-semibold">Home</Link>
                        </li>

                        <li class="relative group" @mouseenter="activeDropdown = 'men'"
                            @mouseleave="activeDropdown = null">
                            <Link href="/shop" class="hover:text-secondary font-semibold flex items-center">
                                Men
                                <i
                                    :class="['fas ml-1 text-xs', activeDropdown === 'men' ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                            </Link>
                            <transition name="fade">
                                <ul v-show="activeDropdown === 'men'"
                                    class="absolute left-0 bg-white text-black space-y-2 mt-0 p-2 rounded shadow-lg border-t-2 border-primary">
                                    <li>
                                        <Link href="/shop"
                                            class="min-w-40 block px-4 py-2 hover:bg-primary hover:text-white rounded transition">
                                            Men Item 1</Link>
                                    </li>
                                    <li>
                                        <Link href="/shop"
                                            class="min-w-40 block px-4 py-2 hover:bg-primary hover:text-white rounded transition">
                                            Men Item 2</Link>
                                    </li>
                                </ul>
                            </transition>
                        </li>

                        <li class="relative group" @mouseenter="activeDropdown = 'women'"
                            @mouseleave="activeDropdown = null">
                            <Link href="/shop" class="hover:text-secondary font-semibold flex items-center">
                                Women
                                <i
                                    :class="['fas ml-1 text-xs', activeDropdown === 'women' ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                            </Link>
                            <transition name="fade">
                                <ul v-show="activeDropdown === 'women'"
                                    class="absolute left-0 bg-white text-black space-y-2 mt-0 p-2 rounded shadow-lg border-t-2 border-primary">
                                    <li>
                                        <Link href="/shop"
                                            class="min-w-40 block px-4 py-2 hover:bg-primary hover:text-white rounded transition">
                                            Women Item 1</Link>
                                    </li>
                                    <li>
                                        <Link href="/shop"
                                            class="min-w-40 block px-4 py-2 hover:bg-primary hover:text-white rounded transition">
                                            Women Item 2</Link>
                                    </li>
                                </ul>
                            </transition>
                        </li>

                        <li>
                            <Link href="/produk" class="hover:text-secondary font-semibold">Produk</Link>
                        </li>
                        <li>
                            <Link href="/checkout" class="hover:text-secondary font-semibold">Checkout</Link>
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
                        <Link href="/cart">
                            <img src="assets/images/cart-shopping.svg" alt="Cart"
                                class="h-6 w-6 transition group-hover:scale-110 brightness-200">
                        </Link>
                        <div
                            class="absolute right-0 mt-1 w-80 bg-white shadow-xl p-4 rounded hidden group-hover:block text-black border-t-2 border-primary">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between pb-4 border-b">
                                    <div class="flex items-center">
                                        <img src="/assets/images/single-product/1.jpg"
                                            class="h-12 w-12 object-cover rounded mr-2">
                                        <div>
                                            <p class="font-semibold text-sm">Summer dress</p>
                                            <p class="text-xs">Qty: 1</p>
                                        </div>
                                    </div>
                                    <p class="font-semibold">$25.00</p>
                                </div>
                            </div>
                            <Link href="/cart"
                                class="block text-center mt-4 bg-primary text-white py-2 rounded-full font-semibold hover:bg-black transition">
                                Go to Cart</Link>
                        </div>
                    </div>

                    <button @click="isSearchOpen = !isSearchOpen" class="text-white hover:text-secondary transition">
                        <img src="assets/images/search-icon.svg" alt="Search" class="h-6 w-6 brightness-200">
                    </button>

                    <transition name="slide-fade">
                        <div v-if="isSearchOpen"
                            class="absolute top-full right-0 mt-2 w-64 bg-white shadow-lg p-2 rounded">
                            <input type="text" class="w-full p-2 border border-gray-300 rounded text-black outline-none"
                                placeholder="Search products...">
                        </div>
                    </transition>
                </div>
            </div>

            <transition name="mobile-slide">
                <nav v-if="isMobileMenuOpen"
                    class="mobile-menu flex flex-col items-center space-y-4 lg:hidden bg-gray-dark text-white p-6 pb-10">
                    <ul class="w-full text-center">
                        <li>
                            <Link href="/" class="hover:text-secondary font-bold block py-2">Home</Link>
                        </li>

                        <li class="border-b border-gray-700">
                            <button @click="mobileMenOpen = !mobileMenOpen"
                                class="w-full hover:text-secondary font-bold py-3 flex justify-center items-center">
                                Men <i
                                    :class="['fas ml-2 text-xs', mobileMenOpen ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                            </button>
                            <div v-show="mobileMenOpen" class="bg-gray-800 rounded-lg mb-2">
                                <Link href="/shop" class="block py-2">Shop Men</Link>
                                <Link href="/shop" class="block py-2">Men Item 1</Link>
                            </div>
                        </li>

                        <li class="border-b border-gray-700">
                            <button @click="mobileWomenOpen = !mobileWomenOpen"
                                class="w-full hover:text-secondary font-bold py-3 flex justify-center items-center">
                                Women <i
                                    :class="['fas ml-2 text-xs', mobileWomenOpen ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                            </button>
                            <div v-show="mobileWomenOpen" class="bg-gray-800 rounded-lg mb-2">
                                <Link href="/shop" class="block py-2">Shop Women</Link>
                                <Link href="/shop" class="block py-2">Women Item 1</Link>
                            </div>
                        </li>

                        <li>
                            <Link href="/produk" class="hover:text-secondary font-bold block py-3">Produk</Link>
                        </li>
                    </ul>

                    <div class="flex flex-col w-full space-y-3 pt-4">
                        <Link href="/register" class="bg-primary text-white py-3 rounded-full font-bold text-center">
                            Register</Link>
                        <Link href="/login"
                            class="bg-transparent border border-white text-white py-3 rounded-full font-bold text-center">
                            Login</Link>
                        <div class="relative pt-2">
                            <input type="text"
                                class="w-full p-3 rounded-full bg-white text-black text-center outline-none"
                                placeholder="Search products...">
                        </div>
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
                                <Link href="/cart" class="hover:text-amber-500">Cart</Link>
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