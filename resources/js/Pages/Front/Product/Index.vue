<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import debounce from 'lodash/debounce';
import { useCartStore } from '@/Stores/cart';

const cartStore = useCartStore();

// PrimeVue Components (Explicit imports to help linter/auto-import)
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Image from 'primevue/image';

const props = defineProps({
    menu: String,
    title: String,
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || null);

const filterProducts = () => {
    router.get(route('product.index'), {
        search: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch(search, debounce(() => {
    filterProducts();
}, 300));

const selectCategory = (slug) => {
    selectedCategory.value = selectedCategory.value === slug ? null : slug;
    filterProducts();
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <div class="sticky top-24 space-y-8">
                        <!-- Search -->
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white mb-4">
                                Search</h3>
                            <IconField class="w-full">
                                <InputIcon><i class="pi pi-search" /></InputIcon>
                                <InputText v-model="search" placeholder="Find products..." class="w-full !rounded-xl" />
                            </IconField>
                        </div>

                        <!-- Categories -->
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-gray-900 dark:text-white mb-4">
                                Category</h3>
                            <div class="flex flex-col gap-2">
                                <button v-for="cat in categories" :key="cat.id" @click="selectCategory(cat.slug)"
                                    class="group flex items-center justify-between p-3 rounded-xl border transition-all duration-300 text-left"
                                    :class="selectedCategory === cat.slug
                                        ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200'
                                        : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-rose-400'">
                                    <span class="text-xs font-bold uppercase tracking-wider">{{ cat.name }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full"
                                        :class="selectedCategory === cat.slug ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-700'">
                                        {{ cat.products_count }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Product Grid -->
                <main class="flex-1">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex flex-col">
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{
                                props.title }}</h1>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Showing {{
                                products.from || 0 }} - {{ products.to || 0 }} of {{ products.total }} results</p>
                        </div>
                    </div>

                    <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div v-for="product in products.data" :key="product.id"
                            class="group relative bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 hover:-translate-y-2">
                            <!-- Image Area -->
                            <div class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                <img v-if="product.images.length > 0" :src="`/storage/${product.images[0].image}`"
                                    :alt="product.name"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="pi pi-image text-5xl"></i>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <Tag v-if="product.category" :value="product.category.name" rounded
                                        class="!bg-white/90 !text-gray-900 !text-[10px] font-black uppercase tracking-widest backdrop-blur-sm px-3" />
                                </div>
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <Button @click="cartStore.addItem(product)" label="Add to Cart"
                                        icon="pi pi-shopping-cart"
                                        class="!rounded-full !bg-rose-600 !border-rose-600 !px-6 !font-black !text-xs !uppercase shadow-xl" />
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="p-6">
                                <Link :href="route('product.show', product.slug)">
                                    <h3
                                        class="text-lg font-black text-gray-900 dark:text-white mb-1 group-hover:text-rose-600 transition-colors uppercase tracking-tight">
                                        {{ product.name }}
                                    </h3>
                                </Link>
                                <div class="flex items-center gap-3">
                                    <span class="text-xl font-black text-rose-600">{{
                                        formatCurrencyIndo(product.selling_price)
                                        }}</span>
                                    <!-- Placeholder for original price if needed -->
                                    <span class="text-xs text-gray-400 line-through font-bold">{{
                                        formatCurrencyIndo(product.selling_price * 1.2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else
                        class="flex flex-col items-center justify-center py-20 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                        <div
                            class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                            <i class="pi pi-search text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tighter">No
                            Products Found</h3>
                        <p class="text-gray-500 text-sm italic">Try adjusting your filters or search terms.</p>
                        <Button label="Clear All Filters" text severity="info"
                            class="mt-4 font-black text-xs uppercase underline"
                            @click="search = ''; selectedCategory = null; filterProducts();" />
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links.length > 3" class="mt-12 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <template v-for="(link, k) in products.links" :key="k">
                                <Link v-if="link.url" :href="link.url"
                                    class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all duration-300 border"
                                    :class="link.active
                                        ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200'
                                        : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-500 hover:border-rose-400 hover:text-rose-600'"
                                    v-html="link.label" />
                                <span v-else
                                    class="w-10 h-10 flex items-center justify-center text-xs font-black text-gray-300 select-none"
                                    v-html="link.label" />
                            </template>
                        </nav>
                    </div>
                </main>
            </div>
        </div>
    </FrontLayout>
</template>
