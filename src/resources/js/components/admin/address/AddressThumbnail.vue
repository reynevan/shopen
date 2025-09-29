<script setup>
defineProps({
    address: {type: Object},
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
})

const sizeClasses = {
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
}
</script>

<template>
    <div :class="sizeClasses[size]">
        <div class="font-semibold">{{ address.first_name }} {{ address.last_name }}</div>
        <div class="" v-if="address.company">{{ address.company }}</div>
        <div class="" v-if="address.company_nip">NIP: {{ address.company_nip }}</div>
        <div class="">{{ address.address_line }}</div>
        <div class="">{{ address.postal_code }} {{ address.city }}</div>
        <div class="" v-if="address.phone">tel. {{ address.phone }}</div>
        <div class="" v-if="address.email">{{ address.email }}</div>
    </div>
</template>