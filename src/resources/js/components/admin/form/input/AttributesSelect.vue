<script setup>
import {defineProps, defineModel, computed} from 'vue';
import Select from "@shopen/components/admin/form/input/Select.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

const props = defineProps({
    'attributes': { type: Array },
    'types': { type: Array, default: () => [] },
    'disabled': { type: Boolean, default: false },
})

const model = defineModel();

const filteredAttributes = computed(() => {
    return props.attributes.filter(attribute => {
        if (!props.types || props.types.length === 0) return true;
        return props.types.indexOf(attribute.frontend_type) >= 0;
    })
})

const addAttribute = () => {
    if (props.disabled) return;
    model.value.push({})
}

const removeAttribute = (index) => {
    if (props.disabled) return;
    model.value.splice(index, 1);
}

</script>

<template>
    <div v-for="(attribute, i) in model" class="flex mb-4">
        <Select :disabled="disabled" v-model="attribute.id" class="mr-2" :options="filteredAttributes" label-key="name"/>
        <Button type="ghost" :disabled="disabled" @click="removeAttribute(i)"><i class="bi bi-x-lg text-red-400"></i></Button>
    </div>
    <div class="mt-2">
        <Button :disabled="disabled" @click="addAttribute" size="sm"><i class="bi bi-plus-lg"></i> Dodaj</Button>
    </div>
</template>