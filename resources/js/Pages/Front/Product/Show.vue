<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useCartStore } from '@/Stores/cart';

const cartStore = useCartStore();

// PrimeVue Components
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    product: Object,
    related_products: Array,
    menu: String,
    title: String,
});

const quantity = ref(1);
const activeImage = ref(props.product.images.length > 0 ? props.product.images[0].image : null);

const selectImage = (img) => {
    activeImage.value = img;
};

</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Breadcrumbs -->
            <nav class="flex mb-8 font-black text-gray-400">
                <Link :href="route('home')" class="hover:text-green-600 transition-colors">Home</Link>
                <span class="mx-2">/</span>
                <Link :href="route('product.index')" class="hover:text-green-600 transition-colors">Products</Link>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-white">{{ product.name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
                <!-- Product Gallery -->
                <div class="space-y-4">
                    <div
                        class="aspect-[4/5] rounded-[2.5rem] overflow-hidden bg-gray-100 border border-gray-100 dark:border-gray-800 shadow-2xl shadow-gray-200/50">
                        <img v-if="activeImage" :src="`/storage/${activeImage}`" :alt="product.name"
                            class="w-full h-full object-cover transition-all duration-700 hover:scale-110" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="pi pi-image text-7xl"></i>
                        </div>
                    </div>

                    <div v-if="product.images.length > 1" class="flex gap-4 overflow-x-auto pb-2 no-scrollbar">
                        <button v-for="(img, idx) in product.images" :key="idx" @click="selectImage(img.image)"
                            class="w-24 h-24 flex-shrink-0 rounded-2xl overflow-hidden border-2 transition-all duration-300"
                            :class="activeImage === img.image ? 'border-green-600 ring-4 ring-green-50' : 'border-transparent hover:border-gray-200'">
                            <img :src="`/storage/${img.image}`" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="flex flex-col justify-center">
                    <div class="mb-6">
                        <Tag v-if="product.category" :value="product.category.name" rounded
                            class="!bg-green-50 !text-green-600 !text-[10px] font-black uppercase tracking-widest px-4 mb-4" />
                        <h1 class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter mb-4 uppercase">
                            {{ product.name }}
                        </h1>
                        <div class="flex items-center gap-4 mb-8">
                            <span class="text-4xl font-black text-green-600 tracking-tighter">{{
                                formatCurrencyIndo(product.selling_price) }}</span>
                            <span class="text-lg text-gray-400 line-through font-bold opacity-50">{{
                                formatCurrencyIndo(product.selling_price * 1.2) }}</span>
                            <span
                                class="bg-emerald-50 text-emerald-600 text-[10px] font-black px-2 py-1 rounded-lg uppercase">-20%
                                OFF</span>
                        </div>

                        <div
                            class="pgreen pgreen-green dark:pgreen-invert max-w-none text-gray-500 dark:text-gray-400 leading-relaxed mb-10 italic">
                            {{ product.description || 'No description available for this masterpiece.' }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-8">
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="w-full sm:w-auto">
                                <span class="font-black text-gray-400 block mb-2 px-1">Quantity</span>
                                <InputNumber v-model="quantity" showButtons buttonLayout="horizontal" :min="1"
                                    :max="product.stock"
                                    class="!w-full !rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700"
                                    incrementButtonClass="!bg-gray-50 dark:!bg-gray-800 !text-gray-600 !border-none"
                                    decrementButtonClass="!bg-gray-50 dark:!bg-gray-800 !text-gray-600 !border-none"
                                    inputClass="!w-16 !text-center !font-black !border-none !bg-white dark:!bg-gray-900" />
                            </div>
                            <div class="flex-1 w-full">
                                <span class="hidden sm:block font-black text-transparent mb-2">Spacer</span>
                                <Button @click="cartStore.addItem(product, quantity)" label="Add to Cart"
                                    icon="pi pi-shopping-cart" size="large"
                                    class="!w-full !rounded-2xl !bg-gray-900 !border-gray-900 hover:!bg-black !py-4 !font-black !text-sm !uppercase !tracking-widest shadow-2xl shadow-gray-900/20 transition-all duration-300 active:scale-95" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div v-if="related_products.length > 0">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex flex-col">
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter">Produk
                            Lainnya</h2>
                    </div>
                    <Link :href="route('product.index')"
                        class="text-xs font-black tracking-widest border-b-2 border-green-600 pb-1 hover:text-green-600 transition-colors">
                        Lihat Semua
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="rel in related_products" :key="rel.id"
                        class="group relative bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 hover:-translate-y-2">
                        <Link :href="route('product.show', rel.slug)">
                            <div class="aspect-[4/5] overflow-hidden bg-gray-100 relative">
                                <img v-if="rel.images.length > 0" :src="`/storage/${rel.images[0].image}`"
                                    :alt="rel.name"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="pi pi-image text-3xl"></i>
                                </div>
                                <div
                                    class="absolute inset-x-0 bottom-0 p-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500 bg-gradient-to-t from-black/80 to-transparent">
                                    <Button label="View Detail"
                                        class="w-full !rounded-xl !bg-white !text-gray-900 !border-none !font-black !text-[10px] !uppercase !tracking-widest" />
                                </div>
                            </div>
                            <div class="p-6">
                                <h3
                                    class="text-sm font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight truncate">
                                    {{ rel.name }}</h3>
                                <span class="text-lg font-black text-green-600">{{ formatCurrencyIndo(rel.selling_price)
                                }}</span>
                            </div>
                        </Link>
                    </div>
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
