<script setup>
import AddressThumbnail from "@shopen/components/admin/address/AddressThumbnail.vue";

const props = defineProps({
    defaultShippingAddress: {type: Object },
    defaultBillingAddress: {type: Object },
    shippingAddresses: {type: Array },
    billingAddresses: {type: Array },
})
</script>

<template>
    <div class="flex gap-6">
        <div class="w-1/2">
            <p class="mb-4 pb-2 text-lg font-semibold border-b">Adresy dostawy</p>
            <div class="my-6 border rounded p-4 min-h-20">
                <p class="text-xs uppercase mb-2">ADRES DOMYŚLNY</p>
                <AddressThumbnail size="lg" v-if="defaultShippingAddress" :address="defaultShippingAddress"/>
                <p v-else class="text-gray-400 text-sm py-6">Brak domyślnego adresu</p>
            </div>
            <div v-if="shippingAddresses?.length > 0">
                <p>Dodatkowe adresy dostawy</p>
                <div class="mt-2 flex flex-wrap gap-4">
                    <div class="p-4 border rounded" v-for="address in shippingAddresses">
                        <AddressThumbnail :address="address" />
                    </div>
                </div>
            </div>
        </div>
        <div class="w-1/2">
            <p class="mb-4 pb-2 text-lg font-semibold border-b">Adresy rozliczeniowe</p>
            <div class="my-6 border rounded p-4 min-h-20">
                <p class="text-xs uppercase mb-2">ADRES DOMYŚLNY</p>
                <AddressThumbnail size="lg" v-if="defaultBillingAddress" :address="defaultBillingAddress"/>
                <p v-else class="text-gray-400 text-sm py-6">Brak domyślnego adresu</p>
            </div>
            <div v-if="billingAddresses?.length > 0">
                <p>Dodatkowe adresy rozliczeniowe</p>
                <div class="mt-2 flex flex-wrap gap-4">
                    <div class="p-4 border rounded" v-for="address in billingAddresses">
                        <AddressThumbnail :address="address" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>