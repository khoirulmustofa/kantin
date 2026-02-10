<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    order: Object,
    users: Array,
    products: Array,
    isEdit: Boolean,
});

const toast = useToast();

const form = useForm({
    user_id: props.order?.user_id || null,
    shipping_address: props.order?.shipping_address || '',
    shipping_cost: props.order?.shipping_cost || 0,
    status: props.order?.status || 'pending',
    payment_status: props.order?.payment_status || 'unpaid',
    items: props.order?.order_items.map(item => ({
        product_id: item.product_id,
        name: item.product?.name,
        quantity: item.quantity,
        price: item.price
    })) || []
});

const selectedProduct = ref(null);
const quantityToAdd = ref(1);

const addItem = () => {
    if (!selectedProduct.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Please select a product', life: 3000 });
        return;
    }

    const existingItem = form.items.find(item => item.product_id === selectedProduct.value.id);
    if (existingItem) {
        existingItem.quantity += quantityToAdd.value;
    } else {
        form.items.push({
            product_id: selectedProduct.value.id,
            name: selectedProduct.value.name,
            quantity: quantityToAdd.value,
            price: selectedProduct.value.price
        });
    }

    selectedProduct.value = null;
    quantityToAdd.value = 1;
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const subtotal = computed(() => {
    return form.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
});

const grandTotal = computed(() => {
    return subtotal.value + Number(form.shipping_cost);
});

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.orders.update', props.order.id), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Order updated', life: 3000 }),
        });
    } else {
        form.post(route('admin.orders.store'), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Order created', life: 3000 }),
        });
    }
};

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Processing', value: 'processing' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' }
];

const paymentOptions = [
    { label: 'Unpaid', value: 'unpaid' },
    { label: 'Paid', value: 'paid' },
    { label: 'Failed', value: 'failed' }
];
</script>

<template>

    <Head :title="isEdit ? 'Edit Order' : 'New Order'" />

    <AdminLayout menuActive="orders" :title="isEdit ? 'Edit Order' : 'New Order'">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details & Status -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Order Items Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-shopping-bag text-emerald-500"></i>
                        Order Items
                    </h3>

                    <!-- Add Product Form -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Select Product</label>
                            <Select v-model="selectedProduct" :options="products" filter optionLabel="name"
                                placeholder="Search product..." class="w-full" />
                        </div>
                        <div class="w-full sm:w-24">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Qty</label>
                            <InputNumber v-model="quantityToAdd" :min="1" class="w-full" />
                        </div>
                        <div class="flex items-end">
                            <Button icon="pi pi-plus" label="Add" @click="addItem" severity="secondary"
                                class="w-full sm:w-auto" />
                        </div>
                    </div>

                    <DataTable :value="form.items" class="p-datatable-sm mb-6" stripedRows>
                        <Column field="name" header="Product"></Column>
                        <Column field="price" header="Price">
                            <template #body="slotProps">
                                {{ formatCurrencyIndo(slotProps.data.price) }}
                            </template>
                        </Column>
                        <Column field="quantity" header="Qty">
                            <template #body="slotProps">
                                <InputNumber v-model="slotProps.data.quantity" :min="1" size="small"
                                    inputClass="w-16 text-center" />
                            </template>
                        </Column>
                        <Column header="Total">
                            <template #body="slotProps">
                                <span class="font-bold">{{ formatCurrencyIndo(slotProps.data.price *
                                    slotProps.data.quantity) }}</span>
                            </template>
                        </Column>
                        <Column header="Actions" class="text-right">
                            <template #body="slotProps">
                                <Button icon="pi pi-trash" severity="danger" text rounded
                                    @click="removeItem(slotProps.index)" />
                            </template>
                        </Column>
                    </DataTable>

                    <Message v-if="form.errors.items" severity="error" variant="simple">{{ form.errors.items }}
                    </Message>
                </div>

                <!-- Shipping Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-map-marker text-emerald-500"></i>
                        Shipping Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="text-sm font-bold text-gray-700 dark:text-gray-300">Shipping
                                Address</label>
                            <Textarea id="address" v-model="form.shipping_address" rows="3" class="w-full mt-2"
                                placeholder="Full address details..." />
                            <Message v-if="form.errors.shipping_address" severity="error" variant="simple">{{
                                form.errors.shipping_address }}</Message>
                        </div>
                        <div>
                            <label for="shipping_cost"
                                class="text-sm font-bold text-gray-700 dark:text-gray-300">Shipping Cost</label>
                            <InputNumber id="shipping_cost" v-model="form.shipping_cost" mode="currency" currency="IDR"
                                locale="id-ID" :maxFractionDigits="0" class="w-full mt-2" />
                            <Message v-if="form.errors.shipping_cost" severity="error" variant="simple">{{
                                form.errors.shipping_cost }}
                            </Message>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Actions -->
            <div class="flex flex-col gap-6">
                <!-- Customer Selection Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">Customer</h3>
                    <Select v-model="form.user_id" :options="users" filter optionLabel="name" optionValue="id"
                        placeholder="Select Customer" class="w-full" />
                    <Message v-if="form.errors.user_id" severity="error" variant="simple">{{ form.errors.user_id }}
                    </Message>
                </div>

                <!-- Status Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">Order Status</h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Process Status</label>
                            <Select v-model="form.status" :options="statusOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Payment Status</label>
                            <Select v-model="form.payment_status" :options="paymentOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Total Summary Card -->
                <div class="card p-6 bg-emerald-600 text-white rounded-2xl border border-emerald-500 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 opacity-80 uppercase tracking-wider text-xs">Summary</h3>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span class="font-medium">{{ formatCurrencyIndo(subtotal) }}</span>
                    </div>
                    <div class="flex justify-between mb-4 pb-4 border-b border-emerald-400/30">
                        <span>Shipping</span>
                        <span class="font-medium">{{ formatCurrencyIndo(form.shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold uppercase text-xs opacity-90">Grand Total</span>
                        <span class="text-2xl font-black">{{ formatCurrencyIndo(grandTotal) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <Button :label="isEdit ? 'Update Order' : 'Place Order'"
                        :icon="isEdit ? 'pi pi-save' : 'pi pi-check'" severity="primary" size="large" @click="submit"
                        :loading="form.processing" class="shadow-lg shadow-emerald-500/20" />
                    <Link :href="route('admin.orders.index')" class="w-full">
                        <Button label="Cancel" severity="secondary" variant="text" class="w-full" />
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
