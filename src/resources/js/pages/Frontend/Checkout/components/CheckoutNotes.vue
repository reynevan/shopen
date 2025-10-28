<script setup>

import {useCheckoutStore} from "@shopen/stores/checkout.js";
import FormField from "@shopen/components/frontend/form/FormField.vue";

const checkout = useCheckoutStore();
</script>

<template>
    <FormField label="Uwagi do zamówienia" label-for="order-notes">
        <textarea id="order-notes" class="input" v-model="checkout.notes"></textarea>
    </FormField>
</template>

<style scoped>

</style>