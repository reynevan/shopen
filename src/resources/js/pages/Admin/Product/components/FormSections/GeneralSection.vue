<script setup>
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import CategoryInput from "@shopen/components/admin/form/input/Category/CategoryInput.vue";

const props = defineProps({
    form: {type: Object, required: true},
    categories: { type: Array }
})
</script>

<template>
    <FormField
        :required="true"
        label="Aktywny" label-for="is_active">

        <Toggle v-model="form.attributes.is_active" id="is_active"/>

    </FormField>

    <FormField
        :required="true"
        label-for="name"
        label="Nazwa">
        <Input v-model="form.attributes.name" :required="true" id="name"/>
    </FormField>

    <FormField
        :required="true"
        label-for="sku"
        label="Sku">
        <Input v-model="form.sku" :required="true" id="sku"/>
    </FormField>

    <FormField
        label-for="ean"
        label="EAN">
        <Input v-model="form.ean" id="ean"/>
    </FormField>

    <FormField
        :required="true"
        label-for="url_key"
        label="Klucz URL">
        <Input v-model="form.url_key" :required="true" id="url_key"/>
    </FormField>

    <FormField
        label-for="categories"
        label="Kategorie">

        <CategoryInput v-model="form.category_ids" :categories="categories"/>
    </FormField>

</template>