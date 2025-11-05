<script setup>
import {usePage, useForm} from "@inertiajs/vue3";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import Input from "../input/Input.vue";
import Checkbox from "../input/Checkbox.vue";

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
            <Input v-model="form.first_name" required/>
        </FormField>

        <FormField label="Nazwisko" field="last_name" :error="form.errors.last_name" required>
            <Input type="text" v-model="form.last_name" required/>
        </FormField>

        <FormField label="E-mail" field="email" :error="form.errors.email" required>
            <Input type="email" v-model="form.email" required/>
        </FormField>

        <FormField label="Hasło" field="password" :error="form.errors.password" required>
            <Input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>
        </FormField>

        <div class="block form-field">
            <label for="remember_me" class="inline-flex items-center">
                <Checkbox id="remember_me"
                          required
                          v-model="form.termsAccepted"
                          label="Akceptuję regulamin sklepu"
                          name="remember"/>
            </label>
        </div>

        <div class="mt-4">

            <div class="flex justify-center mt-4">
                <Button type="primary" size="lg" role="submit" full-width>
                    Załóż konto
                </Button>
            </div>
        </div>
    </form>
</template>