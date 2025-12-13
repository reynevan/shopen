<script setup>

import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";

const props = defineProps({
    address: Object,
    errors: Object,
});

</script>

<template>

    <FormField field="first_name" label="Imię" :error="errors.first_name" required>
        <Input id="first_name" autocomplete="given-name" v-model="address.first_name"/>
    </FormField>

    <FormField field="last_name" label="Nazwisko" :error="errors.last_name" required>
        <Input id="last_name" autocomplete="family-name" v-model="address.last_name"/>
    </FormField>
    <FormField field="company" label="Firma" :error="errors.company">
        <Input id="company" autocomplete="given-name" v-model="address.company"/>
    </FormField>

    <FormField field="nip" label="NIP" :error="errors.nip">
        <Input id="nip" autocomplete="family-name" v-model="address.nip"/>
    </FormField>

    <FormField field="address_line" label="Ulica, numer budynku / numer lokalu" :error="errors.address_line" required>
        <Input id="address_line" autocomplete="street-address" v-model="address.address_line"/>
    </FormField>

    <FormField field="postal_code" label="Kod pocztowy" :error="errors.postal_code" required>
        <Input id="postal_code" autocomplete="postal-code" v-model="address.postal_code"/>
    </FormField>

    <FormField field="city" label="Miasto" :error="errors.city" required>
        <Input id="city" autocomplete="address-level2" v-model="address.city"/>
    </FormField>

    <div v-if="address.type === 'shipping'">
        <FormField field="phone" label="Telefon" :error="errors.phone" required>
            <Input id="phone" v-model="address.phone"/>
        </FormField>
    </div>

    <FormField field="is_default" :error="errors.is_default">
        <div class="flex items-center gap-2">
            <Toggle id="is_default" v-model="address.is_default"/>
            <label for="is_default">Adres domyślny</label>
        </div>
    </FormField>
</template>