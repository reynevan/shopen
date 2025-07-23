<script setup>
import Input from "./Input.vue";

const model = defineModel()
const props = defineProps(['required', 'id'])
</script>

<template>
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" v-model="model" :required="required" :id="id">
        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
    </label>
</template>