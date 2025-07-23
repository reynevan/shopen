<script setup>

import AttributesSelect from "../form/input/AttributesSelect.vue";
import FormField from "../form/FormField.vue";
import CategoryInput from "../form/input/Category/CategoryInput.vue";
import Toggle from "../form/input/Toggle.vue";
import Input from "../form/input/Input.vue";
import DateInput from "../form/input/DateInput.vue";
import FormHeader from "../form/FormHeader.vue";
import {Link, router} from "@inertiajs/vue3";

const props = defineProps(['promoCode', 'attributes', 'categories']);


const save = () => {
    if (props.promoCode.id) {
        router.put(route('admin.promo-codes.update', props.promoCode.id), props.promoCode, {
            preserveState: true,
            preserveScroll: true
        });
    } else {
        router.post(route('admin.promo-codes.store', props.promoCode.id), props.promoCode, {
            preserveState: true,
            preserveScroll: true
        });
    }
}

</script>

<template>
    <FormHeader>
        <Link :href="route('admin.promo-codes.index')" class="mr-4">
            <i class="bi bi-arrow-left-short text-2xl"></i> Powrót
        </Link>
        <button @click="save" class="button-primary">Zapisz</button>
    </FormHeader>
    <div class="form">
        <section class="py-10 max-w-4xl mx-auto">
            <FormField label="Aktywny" required>
                <Toggle class="pt-2" v-model="promoCode.is_active"/>
            </FormField>

            <FormField label="Nazwa" required>
                <Input v-model="promoCode.name" required/>
            </FormField>

            <FormField label="Kod" required>
                <Input v-model="promoCode.code" required/>
            </FormField>

            <FormField label="Opis">
                <Input v-model="promoCode.description"/>
            </FormField>

            <FormField label="Typ zniżki" required>
                <select id="discount_type" v-model="promoCode.discount_type">
                    <option value="percentage">Procentowa</option>
                    <option value="fixed_amount">Stała kwota</option>
                </select>
            </FormField>

            <FormField label="Wartość zniżki" required>
                <Input type="number" v-model="promoCode.discount_value" required/>
            </FormField>

            <FormField label="Zakres zastosowania" required>
                <select id="applies_to" v-model="promoCode.applies_to">
                    <option value="cart">Cały koszyk</option>
                    <option value="per_item">Każdy produkt osobno</option>
                </select>
            </FormField>

            <FormField label="Maksymalna wartość zniżki" v-if="promoCode.discount_type === 'percentage'">
                <template #description v-if="promoCode.applies_to === 'cart'">
                    Maksymalna wartość zniżki dla całego koszyka
                </template>
                <template #description v-if="promoCode.applies_to === 'per_item'">
                    Maksymalna wartość zniżki dla każdego produktu
                </template>
                <template #default>
                    <Input type="number" v-model="promoCode.max_discount_amount"/>
                </template>
            </FormField>

            <FormField label="Minimalna wartość zamówienia">
                <Input type="number" v-model="promoCode.minimum_order_value"/>
            </FormField>

            <FormField label="Tylko dla zalogowanych">
                <Toggle class="pt-2" v-model="promoCode.for_logged_users_only"/>
            </FormField>

            <FormField label="Zastosuj dla przecenionych produktów" v-if="promoCode.applies_to === 'per_item'">
                <Toggle class="pt-2" v-model="promoCode.applies_to_discounted"/>
            </FormField>

            <FormField label="Limit użyć">
                <Input type="number" v-model="promoCode.usage_limit"/>
            </FormField>

            <FormField label="Aktywny od">
                <DateInput v-model="promoCode.valid_from"/>
            </FormField>

            <FormField label="Aktywny do">
                <DateInput v-model="promoCode.valid_to"/>
            </FormField>

            <FormField label="Kategorie">
                <CategoryInput :categories="categories" v-model="promoCode.categories"/>
            </FormField>

            <FormField label="Atrybuty">
                <div>
                    <AttributesSelect :attributes="attributes" :conditions="promoCode.attributes"/>
                </div>
            </FormField>
        </section>
    </div>
</template>

<style scoped>

</style>