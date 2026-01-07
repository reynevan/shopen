import { defineStore } from 'pinia'
import { ref } from 'vue'
import {router, usePage} from "@inertiajs/vue3";

export const useCheckoutStore = defineStore('checkout', () => {

    const loading = ref(false)
    const orderLoading = ref(false)
    const page = usePage()
    const customBillingAddress = ref(!!page.props.selectedBillingAddress);
    const notes = ref('');


    const placeOrder = async () => {
        router.post(route('checkout.place-order'), {
            customBillingAddress: customBillingAddress.value,
            notes: notes.value,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['cart'] })
            },
        })
    }

    return {
        notes,
        loading,
        orderLoading,
        customBillingAddress,

        placeOrder,
    }
})