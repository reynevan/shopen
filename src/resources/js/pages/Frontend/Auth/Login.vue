<script setup>
import AuthLayout from "@shopen/layouts/frontend/AuthLayout.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";
import {Link} from "@inertiajs/vue3";

defineOptions({ layout: AuthLayout })



</script>

<template>
    <main class="flex items-center justify-center">
        <div class="w-md bg-body rounded shadow px-8 py-4">
            <h1 class="page-title">Logowanie</h1>
            <LoginForm/>
            <div class="mt-4">Nie masz konta? <Link :href="route('sign-up')" class="link-primary">Załóź konto</Link></div>
        </div>
    </main>
</template>