<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import RegisterForm from "@shopen/components/frontend/form/RegisterForm.vue";

defineOptions({ layout: AppLayout })



</script>

<template>
    <div class="flex">
        <div class="w-1/2 mt-6 px-6 py-4">
            <div class="w-md">
                <h1 class="page-title">Załóż konto</h1>
                <RegisterForm/>
            </div>
        </div>

        <div class="w-1/2">
            <h2 class="text-lg font-semibold">Dlaczego warto założyć konto?</h2>
            <div>Zamawiaj szybciej i wygodniej</div>
            <div>Pełna historia zamówień</div>
            <div>Wszystkie faktury w jednym miejscu</div>
            <div>Dostęp do schowka na produkty</div>
        </div>
    </div>
</template>