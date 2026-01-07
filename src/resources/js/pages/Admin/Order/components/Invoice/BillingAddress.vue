<script setup>
    defineProps(['address'])
</script>

<template>
    <div v-if="address.first_name || address.last_name">
        {{ address.first_name }} {{ address.last_name }}
    </div>
    <div v-if="address.company">{{ address.company }}</div>
    <div v-if="address.nip">NIP: {{ address.nip }}</div>
    <div>{{ address.postal_code }} {{ address.city }}</div>
    <div>{{ address.address_line }}</div>
    <div>tel.: {{ address.phone }}</div>
</template>