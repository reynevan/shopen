<script setup>
import ProductsSelect from "@shopen/components/admin/form/input/Product/ProductsSelect.vue";

const props = defineProps({
    form: {type: Object, required: true},
    product: {type: Object, required: true},
})
</script>

<template>
    <section>
        <div class="form-section-title">
            Produkty powiązane
        </div>
        Podobne
        <ProductsSelect
            @update:selectedProducts="product.related_products = $event"
            v-model="form.related_products_ids"
            :selected-products="product.related_products"/>

        Cross sell
        <ProductsSelect
            @update:selectedProducts="product.cross_sell_products = $event"
            v-model="form.cross_sell_ids"
            :selected-products="product.cross_sell_products"/>

        Up sell
        <ProductsSelect
            @update:selectedProducts="product.up_sell_products = $event"
            v-model="form.up_sell_ids"
            :selected-products="product.up_sell_products"/>
    </section>

</template>