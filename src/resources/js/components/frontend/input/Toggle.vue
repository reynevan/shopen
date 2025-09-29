<script setup>

const model = defineModel()
const props = defineProps({
    required: {type: Boolean, default: false},
    id: {type: String, required: true},
    disabled: {type: Boolean, default: false},
})
</script>

<template>
    <label class="inline-flex items-center mt-2" :class="disabled ? 'cursor-not-allowed' : 'cursor-pointer'">
        <input type="checkbox" class="sr-only peer" v-model="model" :required="required" :id="id" :disabled="disabled" />
        <span class="block relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></span>
    </label>
</template>