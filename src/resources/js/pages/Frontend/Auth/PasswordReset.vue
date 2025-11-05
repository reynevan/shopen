<script setup>
import AuthLayout from "@shopen/layouts/frontend/AuthLayout.vue";
import Input from "../../../components/frontend/input/Input.vue";
import FormField from "../../../components/frontend/form/FormField.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";

defineOptions({ layout: AuthLayout })

const props = defineProps({
    email: {
        type: String,
    },
    token: {
        type: String,
    }
})

const form = useForm({
    email: props.email,
    token: props.token,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('password.store'));
};

</script>

<template>
    <div class="flex items-center justify-center">
        <div class="w-md bg-body rounded shadow px-4 sm:px-8 py-4">
            <h1 class="text-2xl font-semibold my-2 py-2 text-center">Resetowanie hasła</h1>

            <form @submit.prevent="submit">

                <FormField label="Nowe hasło" field="password" :error="form.errors.password">
                    <Input type="password" v-model="form.password" required autofocus/>
                </FormField>

                <FormField label="Powtórz hasło" field="password_confirmation" :error="form.errors.password_confirmation">
                    <Input type="password" v-model="form.password_confirmation" required />
                </FormField>

                <div class="mt-4">

                    <div class="flex justify-center mt-4">
                        <Button type="primary" size="lg" role="submit" full-width>
                            Zmień hasło
                        </Button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</template>