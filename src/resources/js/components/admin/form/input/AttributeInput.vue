<script setup>

import Input from "./Input.vue";
import Multiselect from "./Multiselect.vue";
import DateInput from "./DateInput.vue";
import Toggle from "./Toggle.vue";

const value = defineModel();

const props = defineProps(['attribute'])

</script>

<template>
    <select v-if="attribute.frontend_type === 'select'" v-model="value" :id="'attribute-' + attribute.code"
            class="block w-full py-2.5 sm:py-3 px-4 block focus:ring-0 focus:ring-offset-0 w-full border-gray-200 rounded-lg outline-none sm:text-sm focus:border-accent disabled:opacity-50 disabled:pointer-events-none transition-colors">
        <option></option>
        <option v-for="option in attribute.options"
                :value="option.id">
            {{ option.value }}
        </option>
    </select>
    <Multiselect v-else-if="attribute.frontend_type === 'multiselect'" v-model="value" :options="attribute.options"/>
    <DateInput v-else-if="attribute.frontend_type === 'date'" v-model="value" format="dd-MM-yyyy"
               :id="'attribute-' + attribute.code"/>
    <Toggle v-else-if="attribute.frontend_type === 'bool'" v-model="value" :id="'attribute-' + attribute.code"/>
    <Input v-else v-model="value" :required="!!attribute.is_required" :id="'attribute-' + attribute.code"/>
</template>

<style scoped>

</style>