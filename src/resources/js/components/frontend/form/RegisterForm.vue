<script setup>
import {useForm} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";

const props = defineProps({
    redirectTo: {
        type: String,
        default: null,
    },
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    remember: false,
    redirectTo: props.redirectTo,
});

const submit = () => {
    form.post(route('register'), {
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

        <div class="mt-4">

            <button class="button-primary" type="submit">
                Załóż konto
            </button>
        </div>
    </form>
</template>

<style scoped>

</style>