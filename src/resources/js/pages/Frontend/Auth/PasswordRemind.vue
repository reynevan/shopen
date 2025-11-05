<script setup>
import AuthLayout from "@shopen/layouts/frontend/AuthLayout.vue";
import Input from "../../../components/frontend/input/Input.vue";
import FormField from "../../../components/frontend/form/FormField.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";

defineOptions({ layout: AuthLayout })


const form = useForm({
    email: ''
});

const submit = () => {
    form.post(route('password.email'));
};

</script>

<template>
    <div class="flex items-center justify-center">
        <div class="w-md bg-body rounded shadow px-4 sm:px-8 py-4">
            <h1 class="text-2xl font-semibold my-2 py-2 text-center">Nie pamiętasz hasła?</h1>

            <form @submit.prevent="submit">

                <p class="auth-info">
                    Wpisz adres e‑mail użyty podczas rejestracji. Jeśli istnieje w naszej bazie, otrzymasz e‑mail z instrukcjami zmiany hasła.
                </p>

                <FormField label="Email" field="email" :error="form.errors.email">
                    <Input v-model="form.email" required autofocus/>
                </FormField>

                <div class="mt-4">

                    <div class="flex justify-center mt-4">
                        <Button type="primary" size="lg" role="submit" full-width>
                            Odzyskaj hasło
                        </Button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</template>