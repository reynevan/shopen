<script setup>
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import AttributesSelect from "@shopen/components/admin/form/input/AttributesSelect.vue";
import CategoryMultiselect from "@shopen/components/admin/form/input/Category/CategoryMultiselect/CategoryMultiselect.vue";

const form = defineModel();

const props = defineProps({
    product: {type: Object},
    parent: {type: Object},
    categories: { type: Array },
    attributes: { type: Array },
    brands: { type: Array },

})

const types = [
    {id: 'simple', value: 'Prosty'},
    {id: 'configurable', value: 'Konfigurowalny'},
]

const visibilityOptions = [
    {id: 0, value: 'Brak'},
    {id: 1, value: 'Kategorie'},
    {id: 2, value: 'Wyszukiwanie'},
    {id: 3, value: 'Kategorie i Wyszukiwanie'},
]
</script>

<template>
    <FormField
        label="Aktywny" label-for="is_active">
        <Toggle v-model="form.attributes.is_active" id="is_active"/>
    </FormField>

    <FormField
        label="Produkt wirtualny" label-for="is_virtual">
        <Toggle v-model="form.is_virtual" id="is_virtual"/>
    </FormField>

    <FormField
        v-if="!(parent && parent.id)"
        label-for="type"
        label="Typ">
        <Select v-model="form.type" id="type" :options="types" :disabled="product.id"/>
    </FormField>

    <FormField
        v-if="form.type === 'configurable' && !(parent && parent.id)"
        label-for="configurable_attributes"
        label="Atrybuty konfigurowalne">
        <AttributesSelect v-model="form.configurable_attributes"
                          id="configurable_attributes"
                          :attributes="attributes"
                          :types="['select', 'bool']"
                          :disabled="product.id"
        />
    </FormField>

    <FormField
        label-for="visibility"
        label="Widoczność">
        <Select v-model="form.visibility" id="visibility" :options="visibilityOptions"/>
    </FormField>

    <FormField
        :required="true"
        label-for="name"
        label="Nazwa">
        <Input v-model="form.attributes.name" :required="true" id="name"/>
    </FormField>

    <FormField
        label-for="sku"
        label="Sku">
        <Input v-model="form.sku" id="sku"/>
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

        <CategoryMultiselect v-model="form.category_ids" :categories="categories"/>
    </FormField>

    <FormField
        label="Stan magazynowy" label-for="uses_stock">

        <Toggle v-model="form.uses_stock" id="uses_stock"/>

    </FormField>

    <FormField
        v-show="form.uses_stock"
        label-for="stock_qty"
        label="Ilość w magazynie">
        <Input v-model="form.stock_qty" :required="true" id="stock_qty"/>
    </FormField>

    <FormField
        label-for="brand_id"
        label="Marka">
        <Select v-model="form.brand_id" id="brand_id" :options="brands"/>
    </FormField>

</template>