<script setup>

    const props = defineProps({
        section: { type: String},
        title: { type: String, required: true },
        selected: { type: Boolean, default: false },
    })

    const emits = defineEmits(['onClick'])
</script>

<template>
    <li class="cursor-pointer px-4 py-2 mb-2 rounded transition-all"
        :class="selected ? 'bg-accent' : 'hover:bg-accent/20'"
        @click="emits('onClick')"
    >
        {{ title }}
    </li>
</template>