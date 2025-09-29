<script setup>
import {defineProps, defineModel} from 'vue';
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";

const props = defineProps(['attributes'])
const conditions = defineModel('conditions');

const addCondition = () => {
    conditions.value.push({})
}

const isTypeSelect = (id) => {
    let attr = getAttribute(id);
    return attr && ['select', 'multiselect'].indexOf(attr.frontend_type) >= 0;
}

const isTypeBool = (id) => {
    let attr = getAttribute(id);
    return attr && attr.frontend_type === 'bool';
}

const getAttribute = (id) => {
    for (let i = 0; i < props.attributes.length; i++) {
        if (id === props.attributes[i].id) {
            return props.attributes[i];
        }
    }
}
const getOptions = (attributeId) => {
    let attr = getAttribute(attributeId);
    if (!attr) {
        return [];
    }
    return attr.options;
}

const removeCondition = (index) => {
    conditions.value.splice(index, 1);
}

</script>

<template>
    <div v-for="(condition, i) in conditions" class="flex mb-4">
        <select v-model="condition.attribute_id" class="mr-2">
            <option :value="attribute.id" v-for="attribute in attributes">{{ attribute.name }}</option>
        </select>
        <select v-model="condition.value" v-if="isTypeSelect(condition.attribute_id)">
            <option :value="option.id" v-for="option in getOptions(condition.attribute_id)">
                {{ option.value }}
            </option>
        </select>
        <Toggle v-model="condition.value" v-if="isTypeBool(condition.attribute_id)"/>
        <button @click="removeCondition(i)"><i class="bi bi-x-lg text-red-400"></i></button>
    </div>
    <button @click="addCondition" class="p-2">dodaj</button>
</template>