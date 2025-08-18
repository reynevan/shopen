<script setup>
import CheckoutLayout from "@shopen/layouts/frontend/CheckoutLayout.vue";
import RegisterForm from "@shopen/components/frontend/form/RegisterForm.vue";
import LoginForm from "@shopen/components/frontend/form/LoginForm.vue";
import {Link} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {ref} from "vue";

defineOptions({layout: CheckoutLayout})

const showRegisterForm = ref(false)

</script>

<template>
    <main class="flex flex-col gap-8 sm:flex-row items-start justify-center">
        <div class="w-full sm:w-1/2 bg-body rounded shadow px-8 py-4 mb-6">
            <h1 class="page-title text-center">Logowanie</h1>
            <LoginForm :redirect-to="route('checkout.index')"/>
        </div>
        <div class="w-full sm:w-1/2 mb-12 sm:mb-2 bg-body rounded shadow px-8 pt-4 pb-12 flex flex-col items-center gap-2">
            <h2 class="page-title">Nie mam konta</h2>
            <Link :href="route('checkout.index')" class="block w-full">
                <Button type="secondary" full-width>Kontynuuj bez konta</Button>
            </Link>
            <div class="my-6 w-full flex items-center gap-4 text-neutral-500 text-sm">
                <div class="w-full h-[1px] border-b border-light"></div>
                <div class="whitespace-nowrap">
                    lub
                </div>
                <div class="w-full h-[1px] border-b border-light"></div>
            </div>
            <Button @click="showRegisterForm = true" full-width>Załóż konto</Button>
            <div v-show="showRegisterForm" class="w-full">
                <h2 class="page-title">Rejestracja</h2>
                <RegisterForm :redirect-to="route('checkout.index')"/>
            </div>
        </div>
    </main>
</template>