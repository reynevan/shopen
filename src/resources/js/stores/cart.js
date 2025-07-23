import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import {router} from "@inertiajs/vue3";

export const useCartStore = defineStore('cart', () => {
    const items = ref([])
    const total = ref(0);
    const subtotal = ref(0);
    const shipping = ref(0);
    const loading = ref(false);
    const addingToCart = ref(false);

    const itemsCount = computed(() => {
        return items.value.reduce((count, {quantity}) => count + quantity, 0);
    })

    async function addToCart(productId, quantity = 1) {
        try {
            addingToCart.value = true;
            await axios.post('/api/cart/add-item', {
                id: productId,
                qty: quantity,
            }).finally(() => {
                addingToCart.value = false;
            })
            router.reload({
                only: ['cart'],
                preserveScroll: true
            })
            return true;
        } catch (e) {
            return false;
        }
    }

    async function removeItem(itemId) {
        try {
            await axios.delete('/api/cart/items/' + itemId)
            router.reload({
                only: ['cart'],
                preserveScroll: true
            })
        } catch (e) {
            console.error('Błąd usuwania z koszyka', e)
        }
    }

    async function updateItem(itemId, qty) {
        try {
            await axios.put('/api/cart/items/' + itemId, {
                qty
            })
            router.reload({
                only: ['cart'],
                preserveScroll: true
            })
        } catch (e) {
            console.error('Błąd aktualizacji produktu w koszyku', e)
        }
    }

    return {
        addingToCart,
        items,
        total,
        subtotal,
        shipping,
        loading,
        addToCart,
        removeItem,
        itemsCount,
        updateItem
    }
})