<script setup>

import FormField from "../../../../../components/frontend/form/FormField.vue";
import Input from "../../../../../components/frontend/input/Input.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "../../../../../components/frontend/ui/Button.vue";

const form = useForm({
    subject: '',
    message: '',
    name: '',
    email: '',
    phone: '',
})

const submit = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    })
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="flex flex-col sm:flex-row sm:gap-4">
            <div class="w-full">
                <FormField label="Imię" required :error="form.errors.name">
                    <Input required v-model="form.name"/>
                </FormField>
            </div>
            <div class="w-full">
                <FormField label="Email" required :error="form.errors.email">
                    <Input required type="email" v-model="form.email"/>
                </FormField>
            </div>
        </div>
        <FormField label="Telefon" :error="form.errors.phone">
            <Input v-model="form.phone"/>
        </FormField>
        <FormField label="Temat" required :error="form.errors.subject">
            <Input required v-model="form.subject"/>
        </FormField>
        <FormField label="Wiadomość" required :error="form.errors.message">
            <textarea class="input" rows="6" required v-model="form.message" maxlength="65000"></textarea>
        </FormField>
        <div>
            <Button type="primary" size="lg" role="submit">
                Wyślij
            </Button>
        </div>
    </form>
</template>