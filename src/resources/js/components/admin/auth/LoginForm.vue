<script setup>
import {useForm, Link} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/frontend/form/FormField.vue";


const form = useForm({
    email: '',
    password: '',
    remember: false
});

const submit = () => {
    form.post(route('admin.login'));
};
</script>

<template>
    <form @submit.prevent="submit">

        <FormField label="Email" field="email" :error="form.errors.email">
            <input type="text" v-model="form.email" required autofocus>
        </FormField>

        <FormField label="Hasło" field="password" :error="form.errors.password">
            <input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>
        </FormField>

        <div class="block form-field">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       v-model="form.remember"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">Zapamiętaj mnie</span>
            </label>
        </div>

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