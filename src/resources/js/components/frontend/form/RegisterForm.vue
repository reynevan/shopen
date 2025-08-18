<script setup>
import {usePage, useForm} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";

const props = defineProps({
    redirectTo: {
        type: String,
        default: null,
    },
    user: {
        type: Object,
        default: () => ({
            first_name: '',
            last_name: '',
            email: '',
        }),
    }
});

const page = usePage();

const form = useForm({
    first_name: props.user?.first_name,
    last_name: props.user?.last_name,
    email: props.user?.email,
    password: '',
    remember: false,
    redirectTo: props.redirectTo,
    termsAccepted: false
});

const submit = () => {
    form.post(route('sign-up'), {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <form @submit.prevent="submit">

        <FormField label="Imię" field="first_name" :error="form.errors.first_name" required>
            <input type="text" v-model="form.first_name" required>
        </FormField>

        <FormField label="Nazwisko" field="last_name" :error="form.errors.last_name" required>
            <input type="text" v-model="form.last_name" required>
        </FormField>

        <FormField label="E-mail" field="email" :error="form.errors.email" required>
            <input type="email" v-model="form.email" required>
        </FormField>

        <FormField label="Hasło" field="password" :error="form.errors.password" required>
            <input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>
        </FormField>

        <div class="block form-field">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       v-model="form.termsAccepted"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">Akceptuję regulamin sklepu</span>
            </label>
        </div>

        <div class="mt-4">

            <div class="flex justify-center mt-4">
                <Button type="secondary" role="submit" full-width>
                    Załóż konto
                </Button>
            </div>
        </div>
    </form>
</template>

<style scoped>

</style>