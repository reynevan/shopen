<script setup>
    const props = defineProps({
        name: String,
        selected: Boolean,
    })

    const emits = defineEmits(['onClick'])
</script>

<template>
    <div
        class="flex items-center gap-2 cursor-pointer pr-4 pl-2 py-2 border border-light rounded hover:bg-accent/50 hover:shadow transition-all"
        :class="selected ? 'border-strong bg-accent' : 'text-neutral-400'"
        @click="emits('onClick')">
        <i class="bi bi-x-lg" v-if="selected"></i>
        {{ name }}
    </div>
</template>