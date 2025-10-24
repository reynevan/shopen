<script setup>
import AuthLayout from "@shopen/layouts/frontend/AuthLayout.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";
import {Link} from "@inertiajs/vue3";
import SocialButtons from "@shopen/components/frontend/auth/SocialButtons.vue";

defineOptions({ layout: AuthLayout })

</script>

<template>
    <div class="flex items-center justify-center">
        <div class="w-md bg-body rounded shadow px-4 sm:px-8 py-4">
            <h1 class="text-2xl font-semibold my-2 py-2 text-center">Logowanie</h1>

            <SocialButtons/>

            <LoginForm/>

            <div class="mt-4">Nie masz konta? <Link :href="route('sign-up')" class="link-primary">Załóź konto</Link></div>
        </div>
    </div>
</template>