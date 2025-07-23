<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";

defineOptions({ layout: AppLayout })



</script>

<template>
    <div class="flex">
        <div class="w-1/2 mt-6 px-6 py-4">
            <div class="w-md">
                <LoginForm/>
            </div>
        </div>

        <div class="w-1/2">
            <a href="route('register')">Zarejestruj konto</a>
        </div>
    </div>
</template>