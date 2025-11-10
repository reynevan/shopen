<script setup>
import Button from "@shopen/components/admin/ui/Button.vue";
import { usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import VariantRow from "../VariantRow.vue";

const form = defineModel();

const page = usePage();

const props = defineProps({
    product: {type: Object},
    variants: {type: Array},
    configurable_attributes: {type: Array},
    attributes: {type: Array}
})

const variantProducts = ref(props.variants)

const addVariant = () => {
    variantProducts.value.push({ attributes: {}, price: {}, editing: true});
}

const onRemove = (index) => {
    variantProducts.value.splice(index, 1);
}

</script>

<template>

    <section>
        <div class="flex gap-2 mb-4">
            <Button @click="addVariant">+ Dodaj konfigurację</Button>
        </div>

        <table v-if="variantProducts?.length" class="table-primary w-full">
            <thead>
            <tr>
                <th>Nazwa</th>
                <th>SKU</th>
                <th class="w-30">Cena</th>
                <th v-for="attribute in product.configurable_attributes">{{ attribute.name }}</th>
                <th>Magazyn</th>
                <th class="w-16">Akcje</th>
            </tr>
            </thead>
            <tbody>
            <VariantRow v-for="(variant, index) in variantProducts"
                        :variant="variant"
                        :product="product"
                        @onRemove="onRemove(index)"
                        :attributes="attributes"/>
            </tbody>
        </table>
        <div v-else class="text-gray-400 text-center py-12">
            Brak konfiguracji. Kliknij <span class="underline cursor-pointer" @click="addVariant">tutaj</span>, żeby dodać.
        </div>
    </section>

</template>