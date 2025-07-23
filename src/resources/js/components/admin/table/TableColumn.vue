<script setup>
import {inject, onMounted, getCurrentInstance } from 'vue'

const table = inject('table');
const props = defineProps({
    field: {
        type: String,
        required: false
    },
    label: {
        type: String,
        required: false
    },
    sortable: {
        type: Boolean,
        required: false,
        default: false
    },
    width: {
        type: String
    }
})
onMounted(() => {
    table.addColumn(getCurrentInstance())
})
</script>

<template>

</template>

<style scoped>

</style>