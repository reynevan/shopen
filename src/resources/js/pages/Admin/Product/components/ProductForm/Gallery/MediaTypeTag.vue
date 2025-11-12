<script setup>
    const props = defineProps({
        name: String,
        selected: Boolean,
    })

    const emits = defineEmits(['onClick'])
</script>

<template>
    <div
        class="flex items-center gap-2 cursor-pointer pr-4 pl-2 py-2 hover:bg-accent/50 transition-all"
        :class="selected ? 'bg-accent' : 'text-neutral-400'"
        @click="emits('onClick')">
        <i class="bi bi-x-lg" v-if="!selected"></i>
        <i class="bi bi-check-lg" v-if="selected"></i>
        {{ name }}
    </div>
</template>