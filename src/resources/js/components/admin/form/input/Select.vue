<script setup>
    defineProps({
        options: {type: Array},
        id: {type: String, required: true},
        disabled: {type: Boolean, default: false},
        valueKey: {type: String, default: 'id'},
        labelKey: {type: String, default: 'value'},
    })
    const model = defineModel();
</script>

<template>
    <select id="id" class="border rounded px-4 py-2"
            :class="disabled ? 'cursor-not-allowed opacity-50' : ''"
            v-model="model"
            :disabled="disabled">
        <option v-for="option in options" :value="option[valueKey]" :key="option[valueKey]">{{ option[labelKey] }}</option>
    </select>
</template>