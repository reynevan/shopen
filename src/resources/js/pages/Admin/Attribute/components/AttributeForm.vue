<script setup>
import {useForm} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";
import ColorPicker from "@shopen/components/admin/form/input/ColorPicker.vue";

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
    is_color: props.attribute?.is_color,
    options: props.attribute?.options ?? [],
})

const entityTypes = [
    {id: 'category', value: 'Kategoria'},
    {id: 'product', value: 'Produkt'}
]

const inputTypes = [
    {id: 'bool', value: 'Tak/Nie'},
    {id: 'select', value: 'Select'},
    {id: 'multiselect', value: 'Multiselect'},
    {id: 'number', value: 'Liczba'},
    {id: 'text', value: 'Tekst'},
    {id: 'textarea', value: 'Edytor tekstu'},
    {id: 'price', value: 'Cena'},
    {id: 'date', value: 'Data'},
]

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

const removeOption = (index) => {
    form.options.splice(index, 1)
}

const addOption = () => {
    form.options.push({value: '', color: '#000000'})
}
</script>

<template>
    <ActionsPanel back-route="admin.attributes.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div>

        <section class="pb-6 mb-6border-b border-light">
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
        </section>


        <section class="pb-6 mb-6border-b border-light">
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

            <FormField
                v-if="form.frontend_type === 'select' || form.frontend_type === 'multiselect'"
                label="Kolor" label-for="is_color">
                <Toggle v-model="form.is_color" id="is_color"/>
            </FormField>
        </section>

        <FormField label="Opcje" v-if="form.frontend_type == 'select' || form.frontend_type == 'multiselect'">
            <div class="flex flex-col gap-2 mb-4">
                <div v-for="(option, index) in form.options" class="flex items-center gap-2">
                    <div class="w-full sm:w-1/2">
                        <Input v-model="option.value"/>
                    </div>
                    <ColorPicker v-if="form.is_color" v-model="option.color"/>
                    <Button type="ghost" @click="removeOption(index)">
                        <span class="text-red-400 hover:text-red-600 transition-all"><i class="bi bi-x-lg"></i></span>
                    </Button>
                </div>
            </div>
            <Button type="primary" @click="addOption"><i class="bi bi-plus"></i> Dodaj opcję</Button>
        </FormField>

    </div>
</template>