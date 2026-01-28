<script setup>

const props = defineProps({
    address: {
        type: Object
    }
})

</script>

<template>
    <div>
        <div class="address-line">{{ address.first_name }} {{ address.last_name }}</div>
        <div class="address-line" v-if="address.company">{{ address.company }}</div>
        <div class="address-line" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
        <div class="address-line">{{ address.address_line }}</div>
        <div class="address-line">{{ address.postal_code }} {{ address.city }}</div>
        <div class="address-line" v-if="address.phone">tel. {{ address.phone }}</div>
        <div class="address-line" v-if="address.email">{{ address.email }}</div>
    </div>
</template>