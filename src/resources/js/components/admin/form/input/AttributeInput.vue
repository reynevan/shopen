<script setup>

import Input from "./Input.vue";
import Multiselect from "./Multiselect.vue";
import Select from "./Select.vue";
import DateInput from "./DateInput.vue";
import Toggle from "./Toggle.vue";

const value = defineModel();

const props = defineProps(['attribute'])

</script>

<template>
    <Select v-if="attribute.frontend_type === 'select'" v-model="value" :id="'attribute-' + attribute.code" :options="attribute.options"/>
    <Multiselect v-else-if="attribute.frontend_type === 'multiselect'" v-model="value" :options="attribute.options"/>
    <DateInput v-else-if="attribute.frontend_type === 'date'" v-model="value" format="dd-MM-yyyy"
               :id="'attribute-' + attribute.code"/>
    <Toggle v-else-if="attribute.frontend_type === 'bool'" v-model="value" :id="'attribute-' + attribute.code"/>
    <Input v-else v-model="value" :required="!!attribute.is_required" :id="'attribute-' + attribute.code"/>
</template>

<style scoped>

</style>