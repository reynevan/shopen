<script setup>
import ProductsSelect from "@shopen/components/admin/form/input/Product/ProductsSelect.vue";

const props = defineProps({
    form: {type: Object, required: true},
    product: {type: Object, required: true},
})

const emits = defineEmits(['update:related', 'update:crossSell', 'update:upSell']);
</script>

<template>
    <section class="flex flex-col gap-6">
        <div class="border-b border-light mb-4 pb-4">
            <h3 class="text-lg mb-2">Podobne</h3>
            <ProductsSelect
                @update:selectedProducts="emits('update:related', $event)"
                v-model="form.related_products_ids"
                :selected-products="product.related_products"/>
        </div>

        <div class="border-b border-light mb-4 pb-4">
            <h3 class="text-lg mb-2">Cross sell</h3>
            <ProductsSelect
                @update:selectedProducts="emits('update:crossSell', $event)"
                v-model="form.cross_sell_ids"
                :selected-products="product.cross_sell_products"/>
        </div>

        <div class="">
            <h3 class="text-lg mb-2">Up sell</h3>
            <ProductsSelect
                @update:selectedProducts="emits('update:upSell', $event)"
                v-model="form.up_sell_ids"
                :selected-products="product.up_sell_products"/>
        </div>
    </section>

</template>