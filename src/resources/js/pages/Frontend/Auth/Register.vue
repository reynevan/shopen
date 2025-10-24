<script setup>
import AuthLayout from "@shopen/layouts/frontend/AuthLayout.vue";
import RegisterForm from "@shopen/components/frontend/form/RegisterForm.vue";
import IconCheck from "@shopen/components/icons/IconCheck.vue";
import SocialButtons from "@shopen/components/frontend/auth/SocialButtons.vue";

defineOptions({layout: AuthLayout})

defineProps({
    user: { type: Object, default: {} },
})

</script>

<template>
    <div class="flex flex-col sm:flex-row ">
        <div class="w-full mb-12 sm:mb-2 sm:w-1/2 bg-body rounded shadow px-4 sm:px-8 pt-4 pb-12">
            <h1 class="text-2xl font-semibold my-2 py-2 text-center">Załóż konto</h1>

            <SocialButtons/>

            <RegisterForm :user="user"/>
        </div>

        <div class="w-full sm:w-1/2 flex justify-center">
            <div class="py-4">
                <h2 class="text-lg font-semibold mb-4">Dlaczego warto założyć konto?</h2>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <IconCheck/>
                        Zamawiaj szybciej i wygodniej
                    </div>
                    <div class="flex items-center gap-2">
                        <IconCheck/>
                        Pełna historia zamówień
                    </div>
                    <div class="flex items-center gap-2">
                        <IconCheck/>
                        Wszystkie faktury w jednym miejscu
                    </div>
                    <div class="flex items-center gap-2">
                        <IconCheck/>
                        Dostęp do schowka na produkty
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>