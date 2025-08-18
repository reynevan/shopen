<script setup>
defineProps({
    width: {type: String, default: 'w-full'}
})
</script>

<template>
    <div class="border rounded mb-4 shadow w-full" :class="[$slots.header ? '' : 'p-4', 'sm:' + width]">
        <div v-if="$slots.header" class="bg-gray-50 text-neutral-600 px-4 py-2 shadow uppercase text-sm tracking-wider">
            <slot name="header"/>
        </div>
        <div class="p-4">
            <slot name="default"/>
        </div>
    </div>

</template>