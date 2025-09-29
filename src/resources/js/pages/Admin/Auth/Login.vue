<script setup>
import AuthLayout from "@shopen/layouts/admin/AuthLayout.vue";
import LoginForm from "@shopen/components/admin/auth/LoginForm.vue";

defineOptions({layout: AuthLayout})


</script>
<template>
    <main class="flex items-center justify-center">
        <div class="w-md bg-body rounded shadow px-4 sm:px-8 py-4">
            <h1 class="text-2xl font-semibold my-2 py-2 text-center">Logowanie</h1>
            <LoginForm/>
        </div>
    </main>
</template>