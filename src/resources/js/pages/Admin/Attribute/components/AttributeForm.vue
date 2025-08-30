<script setup>
import {useForm} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";

const props = defineProps({
    attribute: {
        type: Object
    }
})
const form = useForm({
    id: props.attribute?.id,
    name: props.attribute?.name,
    sort_order: props.attribute?.sort_order,
    entity_type: props.attribute?.entity_type,
    backend_type: props.attribute?.backend_type,
    frontend_type: props.attribute?.frontend_type,
    code: props.attribute?.code,
    units: props.attribute?.units,
    is_filterable: props.attribute?.is_filterable,
    is_searchable: props.attribute?.is_searchable,
    is_system: props.attribute?.is_system,
    is_required: props.attribute?.is_required,
    is_visible_in_details: props.attribute?.is_visible_in_details,
    is_used_in_list: props.attribute?.is_used_in_list,
    options: props.attribute?.options,
})

const entityTypes = {
    category: 'Kategoria',
    product: 'Produkt',
}

const inputTypes = {
    bool: 'Tak/Nie',
    select: 'Select',
    multiselect: 'Multiselect',
    number: 'Liczba',
    text: 'Tekst',
    textarea: 'Edytor tekstu',
    price: 'Cena',
    date: 'Data'
}

const save = async () => {
    if (form.id) {
        form.put(route('admin.attributes.update', form.id), {
            preserveState: true,
            preserveScroll: true
        })
    } else {
        form.post(route('admin.attributes.store'), {
            preserveState: true,
            preserveScroll: true
        })
    }
}
</script>

<template>
    <ActionsPanel back-route="admin.attributes.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div>

        <FormField
            :required="true"
            :error="form.errors.name"
            label="Nazwa" label-for="name">
            <Input v-model="form.name" id="name"/>
        </FormField>

        <FormField
            :required="true"
            :error="form.errors.code"
            label="Kod" label-for="code">
            <Input v-model="form.code" id="code"/>
        </FormField>


        <FormField
            :required="true"
            :error="form.errors.entity_type"
            label="Typ" label-for="entity_type">
            <Select v-model="form.entity_type" :disabled="!!form.id" id="entity_type" :options="entityTypes"/>
        </FormField>

        <FormField
            :required="true"
            :error="form.errors.frontend_type"
            label="Input" label-for="frontend_type">
            <Select v-model="form.frontend_type" :disabled="!!form.id" id="frontend_type" :options="inputTypes"/>
        </FormField>


        <FormField
            label="Kolejność" label-for="sort_order">
            <Input v-model="form.sort_order" type="number" id="sort_order"/>
        </FormField>

        <FormField
            label="Użyj w filtrach" label-for="is_filterable">
            <Toggle v-model="form.is_filterable" id="is_filterable"/>
        </FormField>

        <FormField
            label="Użyj w wyszukiwaniu" label-for="is_searchable">
            <Toggle v-model="form.is_searchable" id="is_searchable"/>
        </FormField>

        <FormField
            label="Widoczny na stronie produktu" label-for="is_visible_in_details">
            <Toggle v-model="form.is_visible_in_details" id="is_visible_in_details"/>
        </FormField>

        <FormField
            label="Widoczny na liście produktów" label-for="is_used_in_list">
            <Toggle v-model="form.is_used_in_list" id="is_used_in_list"/>
        </FormField>
    </div>
</template>