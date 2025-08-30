<script setup>

import IconCircle from "@shopen/components/icons/IconCircle.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import {usePage} from "@inertiajs/vue3";
import {computed, onMounted} from "vue";
import {usePaymentStore} from "@shopen/stores/payment.js";

const payment = usePaymentStore()

const props = defineProps(['method']);
const page = usePage();

const isSelected = computed(() => page.props.selectedPaymentMethod === props.method.key);

const selectMethod = () => {
    payment.selectMethod(props.method.key)
}

let paymentsClient = null
let googleScriptEl = null

// loader skryptu Google Pay -> zwraca Promise, które resolve gdy skrypt załadowany
function loadGooglePayScript() {
    const src = 'https://pay.google.com/gp/p/js/pay.js'
    // jeśli już załadowany -> resolve od razu
    const existing = document.querySelector(`script[src="${src}"]`)
    if (existing) {
        // jeśli skrypt już jest i gotowy, resolve; jeśli nie gotowy, poczekamy na load
        if ((window.google && window.google.payments) || existing.getAttribute('data-loaded') === 'true') {
            return Promise.resolve(existing)
        }
        return new Promise((resolve, reject) => {
            existing.addEventListener('load', () => resolve(existing))
            existing.addEventListener('error', reject)
        })
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement('script')
        script.src = src
        script.async = true
        script.defer = true
        script.addEventListener('load', () => {
            script.setAttribute('data-loaded', 'true')
            resolve(script)
        })
        script.addEventListener('error', (e) => reject(e))
        document.head.appendChild(script)
        googleScriptEl = script
    })
}

// inicjalizacja Google Pay po załadowaniu skryptu
async function initGooglePay() {
    if (!window.google || !window.google.payments) {
        console.error('Google Pay JS nie jest dostępny')
        return
    }

    paymentsClient = new window.google.payments.api.PaymentsClient({ environment: 'TEST' })

    const baseRequest = { apiVersion: 2, apiVersionMinor: 0 }
    const allowedCardNetworks = ['VISA', 'MASTERCARD']
    const allowedAuthMethods = ['PAN_ONLY', 'CRYPTOGRAM_3DS']

    const tokenizationSpecification = {
        type: 'PAYMENT_GATEWAY',
        parameters: {
            gateway: 'stripe', // dostosuj do Twojego PSP
            'stripe:version': '2020-08-27',
            'stripe:publishableKey': 'pk_test_...'
        }
    }

    const allowedPaymentMethods = [{
        type: 'CARD',
        parameters: { allowedAuthMethods, allowedCardNetworks },
        tokenizationSpecification
    }]

    try {
        const isReady = await paymentsClient.isReadyToPay(Object.assign({}, baseRequest, { allowedPaymentMethods }))
        if (!isReady.result) {
            console.log('Google Pay niedostępny na tym urządzeniu/przeglądarce')
            return
        }

        const button = paymentsClient.createButton({
            onClick: onGooglePayClicked,
            buttonColor: 'default',
            buttonType: 'long'
        });
        document.getElementById('google-pay-btn-container').appendChild(button);

    } catch (err) {
        console.error('isReadyToPay error', err)
    }
}

function onGooglePayClicked() {
    const baseRequest = { apiVersion: 2, apiVersionMinor: 0 }
    const transactionInfo = {
        totalPriceStatus: 'FINAL',
        totalPrice: page.props.summary.total_raw,
        currencyCode: 'PLN'
    }

    const paymentDataRequest = Object.assign({}, baseRequest, {
        allowedPaymentMethods: [{
            type: 'CARD',
            parameters: {
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                allowedCardNetworks: ['VISA', 'MASTERCARD']
            },
            tokenizationSpecification: {
                type: 'PAYMENT_GATEWAY',
                parameters: {
                    gateway: 'stripe',
                    'stripe:version': '2020-08-27',
                    'stripe:publishableKey': 'pk_test_...'
                }
            }
        }],
        transactionInfo,
        merchantInfo: { merchantName: 'Twój Sklep' }
    })

    paymentsClient.loadPaymentData(paymentDataRequest)
        .then(paymentData => {
            // wyciągnij token i wyślij do backendu
            const token = paymentData.paymentMethodData.tokenizationData.token
            axios.post('/payments/google-pay', { token, paymentData })
                .then(r => {
                    // obsługa sukcesu
                })
                .catch(err => {
                    // obsługa błędu backendu
                })
        })
        .catch(err => {
            // np. użytkownik anulował
            console.error('loadPaymentData error', err)
        })
}

onMounted(async () => {
    if (typeof window === 'undefined') return // SSR safety

    try {
        await loadGooglePayScript()
        await initGooglePay()
    } catch (e) {
        console.error('Nie udało się załadować Google Pay:', e)
    }
})

</script>

<template>
<div>
    <Teleport to="#place-order-buttons-container">
        <div id="google-pay-btn-container"></div>
    </Teleport>
    <div
         class="flex justify-between items-center px-4 py-2 mb-2 cursor-pointer rounded transition-colors hover:bg-accent/10 border"
         :class="[isSelected ? 'bg-accent/10 border-strong': 'border-transparent']"
         @click="selectMethod">
        <div class="flex">
            <div class="pt-1 mr-2 text-neutral-700">
                <IconCheckCircle v-if="isSelected"/>
                <IconCircle v-else></IconCircle>
            </div>
            <div>
                <div class="font-semibold">{{ method.name }}</div>
                <div class="text-neutral-500" v-if="method.description">{{ method.description }}</div>
            </div>
        </div>
        <div class="w-20 text-right" v-if="method.price">
            {{ method.price }}
        </div>
        <slot name="default"/>
    </div>
</div>
</template>

<style scoped>

</style>