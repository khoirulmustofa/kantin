import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export const useCartStore = defineStore('cart', () => {

    // State: menyimpan daftar product dalam keranjang
    const items = ref([]);

    // Ambil data cart dari localStorage saat pertama kali dijalankan
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        items.value = JSON.parse(savedCart);
    }

    // Simpan otomatis ke localStorage setiap ada perubahan pada items
    watch(items, (newItems) => {
        localStorage.setItem('cart', JSON.stringify(newItems));
    }, { deep: true });

    // Menghitung total jumlah barang (berdasarkan quantity)
    const totalItems = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    // Menghitung total harga (price × quantity)
    const totalPrice = computed(() => {
        return items.value.reduce((total, item) => total + (item.price * item.quantity), 0);
    });

    // Menambahkan product ke keranjang
    const addItem = (product, quantity = 1) => {
        const existingItem = items.value.find(item => item.id === product.id);

        if (existingItem) {
            // Jika sudah ada, tambahkan quantity
            existingItem.quantity += quantity;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            items.value.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                price: parseFloat(product.selling_price),
                image: product.images && product.images.length > 0
                    ? product.images[0].image
                    : null,
                quantity: quantity,
                category: product.category
                    ? product.category.name
                    : null
            });
        }
    };

    // Menghapus product dari keranjang berdasarkan ID
    const removeItem = (productId) => {
        items.value = items.value.filter(item => item.id !== productId);
    };

    // Mengubah jumlah product
    const updateQuantity = (productId, quantity) => {
        const item = items.value.find(item => item.id === productId);

        if (item) {
            item.quantity = quantity;

            // Jika quantity kurang dari atau sama dengan 0, hapus item
            if (item.quantity <= 0) {
                removeItem(productId);
            }
        }
    };

    // Mengosongkan seluruh keranjang
    const clearCart = () => {
        items.value = [];
    };

    // Export state dan function agar bisa dipakai di component
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
