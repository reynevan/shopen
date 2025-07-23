<script setup>
    import IconNoImage from "../../icons/IconNoImage.vue";

    const props = defineProps({
        'size' : {
            type: String,
            default: 'md'
        },
        'product': {
            type: Object
        }
    })

    const sizeClass = {
        'w-[100px] h-[100px]': props.size === 'md',
        'w-[50px] h-[50px]': props.size === 'sm',
    };
</script>

<template>
    <div class="flex items-center justify-center text-no-image-icon" :class="sizeClass">
        <img v-if="product.image" :src="product.image" :alt="product.name" class="object-contain" :class="sizeClass">
        <IconNoImage md v-if="!product.image"></IconNoImage>
    </div>
</template>

<style scoped>

</style>