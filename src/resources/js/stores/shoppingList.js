import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useShoppingListStore = defineStore('shoppingList', () => {
    const isModalOpen = ref(false)
    const product = ref(null)

    function openModal(_product) {
        product.value = _product
        isModalOpen.value = true
    }

    function closeModal() {
        isModalOpen.value = false
        product.value = null
    }

    return { isModalOpen, product, openModal, closeModal }
})