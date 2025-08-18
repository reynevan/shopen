import {defineStore} from 'pinia'
import {ref, computed} from 'vue'
import axios from 'axios'
import {router} from "@inertiajs/vue3";

export const useCartStore = defineStore('cart', () => {
        const items = ref([])
        const total = ref(0);
        const subtotal = ref(0);
        const shipping = ref(0);
        const loading = ref(false);
        const addingToCart = ref({});

        const itemsCount = computed(() => {
            return items.value.reduce((count, {quantity}) => count + quantity, 0);
        })

        async function addToCart(productId, quantity = 1) {
            addingToCart.value[productId] = true;
            router.post(route('api.cart.items.add'), {
                id: productId,
                qty: quantity
            }, {
                only: ['cart'],
                preserveScroll: true,
                onFinish: () => addingToCart.value[productId] = false
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
    }
)