<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";

defineOptions({ layout: AppLayout })


</script>

<template>
    <div class="checkout flex flex-wrap xl:flex-no-wrap justify-center items-start">

        <LoginForm :redirect-to="route('checkout.index')"/>
    </div>
</template>