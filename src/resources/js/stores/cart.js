import {defineStore} from 'pinia'
import {ref} from 'vue'
import {router} from "@inertiajs/vue3";
import {trackAddToCart} from '@shopen/utils/ga4.js'

export const useCartStore = defineStore('cart', () => {
    const loading = ref(false)
    const addingToCart = ref({})
    const items = ref([])
    const subtotal = ref(0)
    const isLoaded = ref(false)

    const setCart = (cartData) => {
        if (cartData && typeof cartData.items !== 'undefined') {
            items.value = cartData.items;
            isLoaded.value = true
        }
        if (cartData && typeof cartData.subtotal !== 'undefined') {
            subtotal.value = cartData.subtotal;
        }
    }

    async function addToCart(product, quantity = 1, options = {}) {

        trackAddToCart(product, quantity);

        addingToCart.value[product.id] = true;
        router.post(route('api.cart.items.add'), {
            id: product.id,
            qty: quantity
        }, {
            only: ['cart'],
            preserveScroll: true,
            onFinish: () => addingToCart.value[product.id] = false,
            ...options
        })
    }

    async function removeItem(itemId) {
        router.delete(route('api.cart.items.delete', itemId), {
            only: ['cart'],
            preserveScroll: true
        })
    }

    async function updateItem(itemId, qty) {
        router.put(route('api.cart.items.update', itemId), {
            qty
        }, {
            only: ['cart'],
            preserveScroll: true
        })
    }

    return {
        addingToCart,
        loading,
        items,
        subtotal,
        isLoaded,
        addToCart,
        removeItem,
        updateItem,
        setCart
    }
}
)