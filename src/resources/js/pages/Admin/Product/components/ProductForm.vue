<script setup>
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import Gallery from "@shopen/components/admin/form/Gallery.vue";
import AttributeInput from "@shopen/components/admin/form/input/AttributeInput.vue";
import DateInput from "@shopen/components/admin/form/input/DateInput.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import ProductFormMenu from "./ProductFormMenu.vue";
import GeneralSection from "./FormSections/GeneralSection.vue";
import RelatedProductsSection from "./FormSections/RelatedProductsSection.vue";

const props = defineProps({
    product: {
        type: Object,
        required: false
    },
    categories: {
        type: Array
    },
    attributes: {
        type: Array
    }
});
const defaultPrice = {
    price: null,
    special_price: null
};
const form = useForm({
    attributes: props.product?.attributes ?? [],
    sku: props.product?.sku,
    ean: props.product?.ean,
    url_key: props.product?.url_key,
    category_ids: props.product?.category_ids ?? [],
    price: props.product?.price ?? defaultPrice,
    related_products_ids: props.product?.related_products_ids ?? [],
    up_sell_ids: props.product?.up_sell_ids ?? [],
    cross_sell_ids: props.product?.cross_sell_ids ?? [],
    images: props.product?.images ?? [],
    uses_stock: props.product?.uses_stock,
    stock_qty: props.product?.stock_qty,
})


const save = async () => {
    form.put(route('admin.products.update', props.product.id), {
        preserveState: true,
        preserveScroll: true
    });
};

const activeSection = ref('general');
</script>

<template>
    <ActionsPanel back-route="admin.products.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div class="flex">
        <ProductFormMenu v-model="activeSection"/>
    </div>
    <div>

        <div class="form">
            <section v-if="attributes.length" class="py-10 max-w-4xl mx-auto">

                <GeneralSection v-show="activeSection === 'general'" :form="form" :categories="categories"/>

                <section>
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

                    <FormField
                        :required="true"
                        label="Stan magazynowy" label-for="uses_stock">

                        <Toggle v-model="form.uses_stock" id="uses_stock"/>

                    </FormField>

                    <FormField
                        v-show="form.uses_stock"
                        :required="true"
                        label-for="stock_qty"
                        label="Ilość w magazynie">
                        <Input v-model="form.stock_qty" :required="true" id="stock_qty"/>
                    </FormField>

                    <TextEditor v-model="form.attributes.description"/>

                    <template v-for="attribute in attributes" :key="attribute.id">
                        <FormField
                            v-if="attribute && !attribute.is_system"
                            :required="!!attribute.is_required"
                            :label="attribute.name"
                            :label-for="'attribute-' + attribute.code"
                        >

                            <AttributeInput v-model="form.attributes[attribute.code]" :attribute="attribute"/>

                        </FormField>
                    </template>
                </section>
            </section>

            <RelatedProductsSection v-show="activeSection === 'related_products'" :product="product" :form="form"/>


            <section class="section">
                <div class="form-section-title">
                    Media
                </div>
                <Gallery v-if="product" :images="form.images"/>
            </section>
        </div>
    </div>

</template>

<style scoped>
</style>
