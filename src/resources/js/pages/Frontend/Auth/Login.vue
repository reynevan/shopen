<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";
import {Link} from "@inertiajs/vue3";

defineOptions({ layout: AppLayout })



</script>

<template>
    <div class="flex items-center justify-center">
        <div class="mt-6 px-6 py-4">
            <div class="w-md">
                <h1 class="page-title">Logowanie</h1>
                <LoginForm/>
                <div class="mt-4">Nie masz konta? <Link :href="route('register')" class="link-primary">Załóź konto</Link></div>
            </div>
        </div>
    </div>
</template>