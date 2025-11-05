<script setup>
import {useForm, Link} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/frontend/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";


const form = useForm({
    email: '',
    password: '',
    remember: true
});

const submit = () => {
    form.post(route('admin.login'));
};
</script>

<template>
    <form @submit.prevent="submit">

        <FormField label="Email" field="email" :error="form.errors.email">
            <Input type="text" v-model="form.email" required autofocus/>
        </FormField>

        <FormField label="Hasło" field="password" :error="form.errors.password">
            <Input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   class="input"
                   required autocomplete="current-password"/>
        </FormField>

        <div class="mt-4">
            <div>
                <Link class="link-primary text-sm"
                      href="route('password.request')">
                    Nie pamiętasz hasła?
                </Link>
            </div>

            <div class="flex justify-center mt-4">
                <Button type="primary" role="submit" full-width>
                    Zaloguj się
                </Button>
            </div>
        </div>
    </form>
</template>

<style scoped>

</style>