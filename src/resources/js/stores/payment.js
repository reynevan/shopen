import {defineStore} from "pinia";
import {router, usePage} from "@inertiajs/vue3";

export const usePaymentStore = defineStore('payment', () => {

    const page = usePage()

    function selectMethod(key) {
        if (page.props.errors.payment_method) {
            page.props.errors.payment_method = null;
        }
        router.put(route('checkout.update-payment-method'),
            {
                'paymentMethod': key
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['selectedPaymentMethod', 'selectedShippingMethod', 'shippingMethods', 'summary']
            })
    }

    return {
        selectMethod
    }
})