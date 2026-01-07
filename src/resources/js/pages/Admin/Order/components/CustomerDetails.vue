<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    order: Object
})
</script>

<template>
    <div class="mb-4 px-4 flex flex-wrap items-start">
        <div class="w-full sm:w-1/2">
            <div class="font-semibold text-lg mb-2">Adres wysyłki</div>
            <div>
                <div class="">{{ order.shipping_address.first_name }} {{
                        order.shipping_address.last_name
                    }}
                </div>
                <div class="">{{ order.shipping_address.postal_code }} {{
                        order.shipping_address.city
                    }}
                </div>
                <div class="">{{ order.shipping_address.address_line }}</div>
                <div class="">tel.: {{ order.shipping_address.phone }}</div>
                <div v-if="order.shipping_address.email" class="">{{ order.shipping_address.email }}</div>
            </div>
        </div>
        <div class="w-full sm:w-1/2">
            <div class="font-semibold text-lg mb-2">Dane do płatności</div>
            <div>
                <div class="">{{ order.billing_address.first_name }} {{
                        order.billing_address.last_name
                    }}
                </div>
                <div class="">{{ order.billing_address.postal_code }} {{ order.billing_address.city }}</div>
                <div class="">{{ order.billing_address.address_line }}</div>
                <div class="">tel.: {{ order.billing_address.phone }}</div>
                <div v-if="order.billing_address.email" class="">{{ order.billing_address.email }}</div>
            </div>
        </div>
    </div>
</template>