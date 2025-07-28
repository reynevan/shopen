<script setup>
import {useForm, Link} from "@inertiajs/vue3";

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
            <Link class="link-primary text-sm"
               href="route('password.request')">
                Nie pamiętasz hasła?
            </Link>

            <button class="button-primary mt-2" type="submit">
                Zaloguj się
            </button>
        </div>
    </form>
</template>

<style scoped>

</style>