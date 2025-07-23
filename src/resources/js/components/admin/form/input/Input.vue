<script setup>
const model = defineModel()
const props = defineProps({
    required: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String
    },
    type: {
        type: String,
        default: 'text'
    },
    class: {
        type: String
    }
});
</script>

<template>
    <input :type="type" :id="props.id" v-model="model" :required="!!props.required" :class="props.class"
           class="block w-full py-2.5 sm:py-3 px-4 block focus:ring-0 focus:ring-offset-0 w-full border border-gray-200 rounded-lg outline-none sm:text-sm focus:border-accent disabled:opacity-50 disabled:pointer-events-none transition-colors">
</template>