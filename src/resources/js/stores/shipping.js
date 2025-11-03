import {defineStore} from 'pinia'
import {ref, watch} from 'vue'
import axios from 'axios'
import {useCheckoutStore} from "./checkout";
import {useAddressStore} from "./address";
import {router, usePage} from "@inertiajs/vue3";
import {trackAddShippingInfo} from "../utils/ga4";

export const useShippingStore = defineStore('shipping', () => {

    const checkout = useCheckoutStore();
    const address = useAddressStore();
    const page = usePage();

    const methods = ref([])
    const deliveryPoint = ref(null);
    const loading = ref(false)

    const shippingAddress = ref({});
    const billingAddress = ref({});

    const customBillingAddress = ref(false);

    watch(customBillingAddress, (newCustomBillingAddress, oldCustomBillingAddress) => {
        if (checkout.errors.billing_address && oldCustomBillingAddress && !newCustomBillingAddress) {
            checkout.errors.billing_address = null;
        }
    })

    const setShippingAddress = (_address) => {
        shippingAddress.value = _address;
        if (checkout.errors.shipping_address) {
            checkout.errors.shipping_address = null;
        }
    }
    const setBillingAddress = (_address) => {
        billingAddress.value = _address;
        if (checkout.errors.billing_address) {
            checkout.errors.billing_address = null;
        }
    }

    const validateShippingAddress = () => {
        return address.validate(shippingAddress.value, {value: {}});
    }

    const validateBillingAddress = () => {
        if (!customBillingAddress.value) {
            return true;
        }
        return address.validate(billingAddress.value, {value: {}});
    }

    function selectMethod(key, deliveryPoint = null) {
        trackAddShippingInfo(usePage().props?.cart?.items, key)
        if (page.props.errors.shipping_method) {
            delete page.props.errors.shipping_method;
        }
        router.put(route('checkout.update-shipping-method'),
            {
                'shippingMethod': key,
                deliveryPoint
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['selectedShippingMethod', 'selectedPaymentMethod', 'deliveryPoint', 'summary', 'paymentMethods']
            })
    }

    const methodPrice = (key) => {
        const method = methods.value.find(m => m.key === key);
        return method ? method.price : null;
    }

    async function fetchMethods() {
        loading.value = true
        try {
            const response = await axios.get('/api/checkout/shipping-methods')
            methods.value = response.data;
        } catch (e) {
            console.error('Błąd pobierania metod dostawy', e)
        } finally {
            loading.value = false
        }
    }

    return {
        methods,
        customBillingAddress,
        shippingAddress,
        billingAddress,
        deliveryPoint,

        fetchMethods,
        selectMethod,
        methodPrice,
        setShippingAddress,
        setBillingAddress,
        validateShippingAddress,
        validateBillingAddress
    }
})