<script setup>

import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";

const props = defineProps(['taxClass']);

const form = useForm({
    id: props.taxClass?.id ?? null,
    name: props.taxClass?.name ?? null,
    code: props.taxClass?.code ?? null,
    rate: props.taxClass?.rate ?? null,
    description: props.taxClass?.description ?? null,
})

const save = () => {
    if (props.taxClass.id) {
        form.put(route('admin.tax-classes.update', props.taxClass.id), {
            preserveState: true,
            preserveScroll: true
        });
    } else {
        form.post(route('admin.tax-classes.store', props.taxClass.id), {
            preserveState: true,
            preserveScroll: true
        });
    }
}

</script>

<template>
    <ActionsPanel back-route="admin.tax-classes.index">
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <div class="form">
        <section class="py-10 max-w-4xl mx-auto">

            <FormField label="Nazwa" required>
                <Input v-model="form.name" required/>
            </FormField>

            <FormField label="Kod" required>
                <Input v-model="form.code" required/>
            </FormField>

            <FormField label="Stawka VAT [%]" required>
                <Input v-model="form.rate" type="number" required/>
            </FormField>

            <FormField label="Opis">
                <Input v-model="form.description"/>
            </FormField>
        </section>
    </div>
</template>