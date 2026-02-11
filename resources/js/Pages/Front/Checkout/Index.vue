<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useToast } from "primevue/usetoast";

const props = defineProps({
    menu: String,
    title: String,
    financialAccounts: Array,
});

const cartStore = useCartStore();
const toast = useToast();

const form = useForm({
    username: '', // WhatsApp number
    name: '',
    financial_account_id: '',
    shipping_address: '',
    items: cartStore.items.map(item => ({
        id: item.id,
        quantity: item.quantity
    })),
});

const submitCheckout = () => {
    form.post(route('checkout.store'), {
        onSuccess: () => {
            cartStore.clearCart();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Order placed successfully!', life: 3000 });
        },
        onError: (errors) => {
            console.error(errors);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong. Please check your details.', life: 3000 });
        }
    });
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-10 tracking-tighter">{{ title }}</h1>

            <div v-if="cartStore.items.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-8">
                    <div
                        class="card p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl shadow-gray-100/50">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-8 tracking-tight">Shipping &
                            Customer Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-black  tracking-widest text-gray-700">WhatsApp
                                    Number</label>
                                <InputText v-model="form.username" placeholder="e.g. 08123456789"
                                    class="!rounded-2xl !py-4 !px-6 !border-gray-100 font-bold"
                                    :class="{ 'p-invalid': form.errors.username }" />
                                <small v-if="form.errors.username" class="text-rose-600 font-bold">{{
                                    form.errors.username }}</small>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-black  tracking-widest text-gray-700">Full
                                    Name</label>
                                <InputText v-model="form.name" placeholder="Your full name"
                                    class="!rounded-2xl !py-4 !px-6 !border-gray-100 font-bold"
                                    :class="{ 'p-invalid': form.errors.name }" />
                                <small v-if="form.errors.name" class="text-rose-600 font-bold">{{ form.errors.name
                                    }}</small>
                            </div>

                            <div class="flex flex-col gap-2 md:col-span-2">
                                <label class="text-xs font-black  tracking-widest text-gray-700">Shipping
                                    Address</label>
                                <Textarea v-model="form.shipping_address" rows="3"
                                    placeholder="Full address details. ex: NFBS Bogor, Asrama 1, Kelas 7A"
                                    class="!rounded-2xl !py-4 !px-6 !border-gray-100 font-bold"
                                    :class="{ 'p-invalid': form.errors.shipping_address }" />
                                <small v-if="form.errors.shipping_address" class="text-rose-600 font-bold">{{
                                    form.errors.shipping_address }}</small>
                            </div>

                            <div class="flex flex-col gap-2 md:col-span-2">
                                <label class="text-xs font-black  tracking-widest text-gray-700">Payment
                                    Method</label>
                                <Select v-model="form.financial_account_id" :options="financialAccounts"
                                    optionLabel="name" optionValue="id" placeholder="Select payment method"
                                    class="!rounded-2xl !border-gray-100 font-bold"
                                    :class="{ 'p-invalid': form.errors.financial_account_id }">
                                    <template #option="slotProps">
                                        <div class="flex flex-col">
                                            <span class="font-black text-rose-600">{{ slotProps.option.name }}</span>
                                            <span class="text-[10px] text-gray-700 tracking-widest ">{{
                                                slotProps.option.account_number }}</span>
                                        </div>
                                    </template>
                                </Select>
                                <small v-if="form.errors.financial_account_id" class="text-rose-600 font-bold">{{
                                    form.errors.financial_account_id
                                    }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="space-y-2">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white px-4 tracking-tight">Order Items
                        </h2>
                        <div v-for="item in cartStore.items" :key="item.id"
                            class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100">
                                    <img v-if="item.image" :src="`/storage/${item.image}`" :alt="item.name"
                                        class="w-full h-full object-cover">
                                    <img v-else src="\assets\images\placeholder.webp" :alt="item.name"
                                        class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-900 dark:text-white  text-xs tracking-tight">
                                        {{ item.name }}
                                    </h3>
                                    <p class="text-sm font-bold text-gray-700  tracking-widest">{{
                                        item.category }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-sm font-black bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full">{{
                                                item.quantity }} x</span>
                                        <span class="text-xs font-black text-gray-900">{{ formatCurrencyIndo(item.price)
                                            }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-gray-900">{{ formatCurrencyIndo(item.price *
                                    item.quantity) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary & Action -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-32 p-5 bg-white rounded-2xl border border-gray-200 shadow-2xl shadow-black/20 text-black">
                        <h2 class="text-2xl font-black tracking-tight mb-8">Order Summary</h2>

                        <div class="space-y-4 mb-10">
                            <div class="flex justify-between items-center opacity-60">
                                <span class="text-sm font-bold  tracking-widest">Subtotal</span>
                                <span class="font-black">{{ formatCurrencyIndo(cartStore.totalPrice) }}</span>
                            </div>

                            <div class="flex justify-between items-center opacity-60">
                                <span class="text-sm font-bold  tracking-widest">Shipping</span>
                                <span class="text-emerald-400 font-black">FREE</span>
                            </div>

                            <div class="pt-8 mt-8 border-t border-white/10 flex justify-between items-baseline">
                                <span class="text-sm font-black  tracking-[0.2em]">Total</span>
                                <div class="text-right">
                                    <span class="text-3xl font-black tracking-tighter text-rose-500">
                                        {{ formatCurrencyIndo(cartStore.totalPrice) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Button @click="submitCheckout" :loading="form.processing"
                            class="!w-full !bg-green-500 !text-white !py-6 !rounded-2xl !font-black ! !tracking-[0.2em] !border-none hover:!bg-rose-500 hover:!text-white transition-all active:scale-95"
                            label="Complete Purchase" />

                        <p class="text-[10px] text-center mt-6 text-white/40 font-bold  tracking-widest">Secure
                            encrypted
                            transactions</p>
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
                <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4  tracking-tighter">Your cart
                    is empty
                </h2>
                <Link :href="route('product.index')"
                    class="bg-gray-900 text-white px-12 py-5 rounded-2xl font-black text-xs  tracking-[0.2em] hover:bg-black transition-all">
                    Explore Products</Link>
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
