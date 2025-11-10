<script setup>

import FormField from "@shopen/components/frontend/form/FormField.vue";
import Input from "@shopen/components/frontend/input/Input.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import VueTurnstile from 'vue-turnstile';

const form = useForm({
    subject: '',
    message: '',
    name: '',
    email: '',
    phone: '',
    token: null
})

const page = usePage()

const submit = () => {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('subject', 'message', 'name', 'email', 'phone');
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
            <vue-turnstile v-if="page.props.turnstile_site_key" :site-key="page.props.turnstile_site_key" v-model="form.token" theme="light" language="pl" />

            <Button type="primary" size="lg" role="submit" :disabled="!form.token">
                Wyślij
            </Button>
        </div>
    </form>
</template>