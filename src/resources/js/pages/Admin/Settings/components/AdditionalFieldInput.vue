<script setup>

import Input from "@shopen/components/admin/form/input/Input.vue";
import Multiselect from "@shopen/components/admin/form/input/Multiselect.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";
import DateInput from "@shopen/components/admin/form/input/DateInput.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import {computed} from "vue";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";

const value = defineModel();

const props = defineProps(['id', 'input', 'options'])

const options = computed(() => {
    let _options = props.options;
    _options.unshift({id: null, value: null});
    return _options;
})

</script>

<template>
    <Select v-if="input === 'select'" v-model="value" :id="id" :options="options"/>
    <Multiselect v-else-if="input === 'multiselect'" v-model="value" :options="options"/>
    <DateInput v-else-if="input === 'date'" v-model="value" format="dd-MM-yyyy" :id="id"/>
    <Toggle v-else-if="input === 'bool'" v-model="value" :id="id"/>
    <TextEditor v-else-if="input === 'textarea'" v-model="value" :id="id"/>
    <Input v-else v-model="value" :id="id"/>
</template>