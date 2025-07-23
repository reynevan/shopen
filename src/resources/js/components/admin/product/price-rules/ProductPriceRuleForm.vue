<script setup>

import {onMounted, ref} from "vue";
import FormField from "../../form/FormField.vue";
import Input from "../../form/input/Input.vue";
import AttributesSelect from "../../form/input/AttributesSelect.vue";
import CategoryInput from "../../form/input/Category/CategoryInput.vue";

const categories = ref([]);
const attributes = ref([]);
const rule = ref({
    attributes: [],
    categories: []
});

const save = () => {
    axios.post('/admin/api/products/price-rules', rule.value)
        .then(response => {

        })
        .finally(() => {

        })
}

onMounted(() => {
    axios.get('/admin/api/categories')
        .then(response => {
            categories.value = response.data.data;
        })
    axios.get('/admin/api/attributes')
        .then(response => {
            attributes.value = response.data.data;
        })
})
</script>

<template>
    <div>
        <section>
            <FormField label="Nazwa" :required="true">
                <Input v-model="rule.name" :required="true"/>
            </FormField>
            <FormField label="Priorytet" :required="true">
                <Input v-model="rule.priority" :required="true"/>
            </FormField>
            <FormField label="Aktywne" :required="true">
                <input type="checkbox" v-model="rule.is_enabled" :required="true"/>
            </FormField>
            <FormField label="Od" :required="true">
                <Input v-model="rule.from_date" :required="true"/>
            </FormField>
            <FormField label="Do" :required="true">
                <Input v-model="rule.to_date" :required="true"/>
            </FormField>
            <FormField label="Rodzaj zniżki" :required="true">
                <select v-model="rule.discount_type">
                    <option value="percent">Procent od ceny</option>
                    <option value="amount">Stała kwota</option>
                </select>
            </FormField>
            <FormField label="Zniżka" :required="true">
                <Input v-model="rule.discount_amount" :required="true"/>
            </FormField>
        </section>
        <FormField label="Kategorie">
            <CategoryInput :categories="categories" :model-value="rule.categories"/>
        </FormField>
        <section class="mb-4">
            <AttributesSelect :attributes="attributes" :conditions="rule.attributes"/>
        </section>
        <section>
            <button @click="save">Zapisz</button>
        </section>
    </div>
</template>

<style scoped>

</style>