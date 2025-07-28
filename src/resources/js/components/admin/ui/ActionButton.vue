<script setup>

    const props = defineProps({
        type: {
            type: String,
            default: 'view'
        }
    });
    const emits = defineEmits(['click']);

    const classAttr = {
        'bg-blue-100 hover:bg-blue-200 text-blue-700': props.type === 'view',
        'bg-red-100 hover:bg-red-200 text-red-700': props.type === 'remove',
        'bg-green-100 hover:bg-green-200 text-green-700': props.type === 'accept',
        'bg-orange-100 hover:bg-orange-200 text-orange-700': props.type === 'cancel',
    }
</script>

<template>
    <button @click="emits('click')" :class="classAttr" class="px-2 py-1 cursor-pointer transition-all shadow hover:shadow-lg">
        <template v-if="!$slots.default">
            <i v-if="type === 'view'" class="bi bi-eye"></i>
            <i v-if="type === 'accept'" class="bi bi-check2"></i>
            <i v-if="type === 'cancel'" class="bi bi-x-lg"></i>
            <i v-if="type === 'remove'" class="bi bi-trash"></i>
        </template>
        <slot/>
    </button>
</template>

<style scoped>

</style>