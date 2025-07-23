<script setup>
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    redirectTo: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    redirectTo: props.redirectTo,
});

const submit = () => {
    form.post(route('login'), {

    });
};
</script>

<template>
    <form @submit.prevent="submit">

        <div class="form-field mb-4">
            <label for="email" class="text-sm">Email:</label>
            <input id="email" v-model="form.email" type="email" name="email" required autofocus autocomplete="username"/>
        </div>

        <div class="form-field mb-4">
            <label for="password" class="text-sm">Hasło</label>

            <input id="password"
                   v-model="form.password"
                   type="password"
                   name="password"
                   required autocomplete="current-password"/>

        </div>

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
            <a class="mb-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="route('password.request')">
                Nie pamiętasz hasła?
            </a>

            <button class="button-primary" type="submit">
                Zaloguj się
            </button>
        </div>
    </form>
</template>

<style scoped>

</style>