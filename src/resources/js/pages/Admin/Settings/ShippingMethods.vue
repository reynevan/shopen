<script setup>
import SettingsLayout from "@shopen/layouts/admin/SettingsLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {router, useForm} from "@inertiajs/vue3";
import Button from "../../../components/admin/ui/Button.vue";
import FormField from "../../../components/admin/form/FormField.vue";
import Toggle from "../../../components/admin/form/input/Toggle.vue";
import Input from "../../../components/admin/form/input/Input.vue";
import SettingsSection from "./components/SettingsSection.vue";

defineOptions({layout: SettingsLayout})

const props = defineProps({
    methods: {type: Array},
})

const form = useForm({
    methods: props.methods
})

const save = () => {
    form.put(route('admin.settings.shipping-methods.update'))
}

</script>

<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Metody wysyłki</PageTitle>
        </template>
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <section>
        <SettingsSection v-for="method in form.methods" :key="method.key" :title="method.name">
            <FormField label="Aktywne">
                <Toggle :id="`active-${method.key}`" v-model="method.active" />
            </FormField>
            <FormField label="Nazwa">
                <Input :id="`title-${method.key}`" v-model="method.title" />
            </FormField>
            <FormField label="Opis">
                <Input :id="`description-${method.key}`" v-model="method.description" />
            </FormField>
            <FormField label="Cena">
                <Input :id="`price-${method.key}`" v-model="method.price" type="number" :min="0"/>
            </FormField>
            <FormField label="Darmowa dostawa">
                <Toggle :id="`free-shipping-available-${method.key}`" v-model="method.free_shipping_available" />
            </FormField>
            <FormField label="Darmowa dostawa od" v-if="method.free_shipping_available">
                <Input :id="`free-shipping-threshold-${method.key}`" v-model="method.free_shipping_threshold" />
            </FormField>
        </SettingsSection>
    </section>
</template>