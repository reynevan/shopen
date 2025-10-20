<script setup>
    const props = defineProps({
        product: {type: Object},
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['sm', 'md'].includes(value)
        },
    })

    const sizeClasses = {
        sm: 'px-1 text-[8px]',
        md: 'py-1 px-2 text-xs',
    }

    const iconClass = 'tracking-widest'
</script>

<template>
    <div class="flex items-start flex-col">
        <div v-if="product.is_new"
             class="bg-gray-800 text-white"
             :class="[iconClass, sizeClasses[size]]">NOWOŚĆ</div>
    </div>
</template>