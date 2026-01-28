<script setup>
    defineProps({
        title: String
    })
</script>

<template>
    <div class="mb-8">
        <div class="flex items-center gap-2">
            <div class="uppercase whitespace-nowrap">{{ title }}</div>
            <div class="w-full h-[1px] bg-gray-300"></div>
        </div>
        <slot/>
    </div>
</template>