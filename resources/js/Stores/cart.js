import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export const useCartStore = defineStore('cart', () => {
    const items = ref([]);

    // Load from localStorage
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        items.value = JSON.parse(savedCart);
    }

    // Persist to localStorage
    watch(items, (newItems) => {
        localStorage.setItem('cart', JSON.stringify(newItems));
    }, { deep: true });

    const totalItems = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    const totalPrice = computed(() => {
        return items.value.reduce((total, item) => total + (item.price * item.quantity), 0);
    });

    const addItem = (product, quantity = 1) => {
        const existingItem = items.value.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            items.value.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                price: parseFloat(product.price),
                image: product.images && product.images.length > 0 ? product.images[0].image : null,
                quantity: quantity,
                category: product.category ? product.category.name : null
            });
        }
    };

    const removeItem = (productId) => {
        items.value = items.value.filter(item => item.id !== productId);
    };

    const updateQuantity = (productId, quantity) => {
        const item = items.value.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
            if (item.quantity <= 0) {
                removeItem(productId);
            }
        }
    };

    const clearCart = () => {
        items.value = [];
    };

    return {
        items,
        totalItems,
        totalPrice,
        addItem,
        removeItem,
        updateQuantity,
        clearCart
    };
});
