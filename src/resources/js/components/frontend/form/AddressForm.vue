<script setup>

import FormField from "@shopen/components/frontend/form/FormField.vue";
import {useAuthStore} from "@shopen/stores/auth.js";

const auth = useAuthStore();

const props = defineProps(['errors', 'address']);

</script>

<template>

    <FormField v-if="!auth.isLoggedIn && address.type === 'shipping'" field="email" label="Adres e-mail" :error="errors.email" required>
        <input type="text" id="email" autocomplete="email" v-model="address.email">
    </FormField>

    <div class="flex flex-wrap sm:flex-nowrap">
        <FormField field="first_name" label="Imię" :error="errors.first_name" class="sm:mr-2 sm:mb-0" required>
            <input type="text" id="first_name" autocomplete="given-name" v-model="address.first_name">
        </FormField>

        <FormField field="last_name" label="Nazwisko" :error="errors.last_name" class="sm:ml-2 ml-0" required>
            <input type="text" id="last_name" autocomplete="family-name" v-model="address.last_name">
        </FormField>
    </div>

    <div class="flex flex-wrap sm:flex-nowrap">
        <FormField field="company" label="Firma" :error="errors.company" class="sm:mr-2 sm:mb-0">
            <input type="text" id="company" autocomplete="given-name" v-model="address.company">
        </FormField>

        <FormField field="nip" label="NIP" :error="errors.nip" class="sm:ml-2 ml-0">
            <input type="text" id="nip" autocomplete="family-name" v-model="address.nip">
        </FormField>
    </div>

    <FormField field="address_line" label="Ulica, numer budynku / numer lokalu" :error="errors.address_line" required>
        <input type="text" id="address_line" autocomplete="street-address" v-model="address.address_line">
    </FormField>


    <div class="flex flex-wrap sm:flex-nowrap">
        <FormField field="postal_code" label="Kod pocztowy" :error="errors.postal_code" class="sm:mr-2 sm:mb-0 w-full sm:w-[130px]" required>
            <input type="text" id="postal_code" autocomplete="postal-code" v-model="address.postal_code">
        </FormField>

        <FormField field="city" label="Miasto" :error="errors.city" class="sm:ml-2 ml-0" required>
            <input type="text" id="city" autocomplete="address-level2" v-model="address.city">
        </FormField>
    </div>

    <div class="flex flex-wrap sm:flex-nowrap" v-if="address.type === 'shipping'">
        <FormField field="phone" label="Telefon" :error="errors.phone" required>
            <input type="text" id="phone" v-model="address.phone">
        </FormField>

        <FormField v-if="!auth.isLoggedIn" field="is_default" label="Adres domyślny" :error="errors.is_default" required>
            <input type="checkbox" id="is_default" v-model="address.is_default">
        </FormField>
    </div>
</template>