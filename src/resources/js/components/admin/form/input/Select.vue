<script setup>
    defineProps({
        options: {type: Array},
        id: {type: String, required: true},
        disabled: {type: Boolean, default: false},
    })
    const model = defineModel();
</script>

<template>
    <select id="id" class="border rounded px-4 py-2"
            :class="disabled ? 'cursor-not-allowed opacity-50' : ''"
            v-model="model"
            :disabled="disabled">
        <option v-for="(label, value) in options" :value="value" :key="value">{{ label }}</option>
    </select>
</template>