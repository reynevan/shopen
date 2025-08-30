<script setup>
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import DateInput from "@shopen/components/admin/form/input/DateInput.vue";

const props = defineProps({
    form: {type: Object, required: true},
    product: {type: Object, required: true},
})
</script>

<template>
    <FormField
        :required="true"
        label-for="price"
        label="Cena">
        <Input v-model="form.price.price" :required="true" id="price" :disabled="product.is_configurable"/>
    </FormField>

    <FormField
        label="Cena specjalna"
        label-for="special_price">
        <Input v-model="form.price.special_price" :required="true" id="special_price"/>
    </FormField>

    <FormField
        label="Cena specjalna od"
        label-for="special_price_from">
        <div class="flex items-center">
            <DateInput v-model="form.price.special_price_from" id="special_price_from"/>
            <div class="mx-2">do</div>
            <DateInput v-model="form.price.special_price_to" id="special_price_to"/>
        </div>
    </FormField>

</template>