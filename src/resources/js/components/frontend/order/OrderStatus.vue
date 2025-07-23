<script setup>
const props = defineProps(['status', 'label']);

const bgClass = {
    'bg-blue-100 text-blue-800': props.status === 'new',
    'bg-yellow-100 text-yellow-800': props.status === 'processing',
    'bg-indigo-100 text-indigo-800': props.status === 'shipped',
    'bg-green-100 text-green-800': props.status === 'delivered',
    'bg-gray-200 text-gray-700': props.status === 'cancelled',
    'bg-red-100 text-red-800': props.status === 'refunded',

};
</script>

<template>
    <div class="px-2 py-1 rounded inline-block font-semibold" :class="bgClass">
        {{ label }}
    </div>
</template>