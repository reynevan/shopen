<script setup>
    defineProps({
        options: {type: Array},
        id: {type: String, required: true},
    })
    const model = defineModel();
</script>

<template>
    <select id="id" class="border rounded px-4 py-2" v-model="model">
        <option v-for="(label, value) in options" :value="value" :key="value">{{ label }}</option>
    </select>
</template>