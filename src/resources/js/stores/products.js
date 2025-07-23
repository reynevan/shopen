import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useProductsStore = defineStore('products', () => {
    // State
    const loading = ref(false)
    const products = ref([])
    const meta = ref({
        current_page: 1,
        per_page: 0,
        total: 0,
        last_page: 0,
    })
    const sort = ref('')
    const controller = ref(null)

    // Getters
    const hasProducts = computed(() => products.value.length > 0)
    const hasMultiplePages = computed(() => meta.value.last_page > 1)

    // Actions
    const abortPreviousRequest = () => {
        if (controller.value) {
            controller.value.abort()
        }
        controller.value = new AbortController()
    }

    const updateMeta = (responseMeta) => {
        meta.value = {
            ...meta.value,
            per_page: responseMeta.per_page,
            total: responseMeta.total,
            last_page: responseMeta.last_page,
        }
    }

    const fetchProducts = async (categoryId, filters = {}) => {
        loading.value = true
        abortPreviousRequest()

        try {
            const response = await axios.get(`/api/categories/${categoryId}/products`, {
                signal: controller.value.signal,
                params: {
                    page: meta.value.current_page,
                    sort: sort.value,
                    filters
                }
            })

            products.value = response.data.data
            updateMeta(response.data.meta)
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Failed to fetch products:', error)
            }
        } finally {
            loading.value = false
        }
    }

    const setPage = (page) => {
        meta.value.current_page = page
    }

    const setSort = (sortValue) => {
        sort.value = sortValue
    }

    const reset = () => {
        products.value = []
        meta.value = {
            current_page: 1,
            per_page: 0,
            total: 0,
            last_page: 0,
        }
        loading.value = false
    }

    return {
        // State
        loading,
        products,
        meta,
        sort,

        // Getters
        hasProducts,
        hasMultiplePages,

        // Actions
        fetchProducts,
        setPage,
        setSort,
        reset,
    }
})