<script setup>
import FormField from "../form/FormField.vue";
import Input from "../form/input/Input.vue";
import Gallery from "../form/Gallery.vue";
import AttributeInput from "../form/input/AttributeInput.vue";
import DateInput from "../form/input/DateInput.vue";
import Toggle from "../form/input/Toggle.vue";
import CategoryInput from "../form/input/Category/CategoryInput.vue";
import FormHeader from "../form/FormHeader.vue";
import TextEditor from "../form/input/TextEditor.vue";
import {ref} from "vue";
import {Link, router} from "@inertiajs/vue3";

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



const save = async () => {
    const images = props.product.images.map(image => {
        console.log(image);
        const imageData = {order: image.order};
        if (image.id) {
            imageData.id = image.id;
        } else {
            imageData.path = image.path;
        }
        return imageData;
    });
    props.product.images = null;
    router.put(route('admin.products.update', props.product.id), {
        product: props.product,
        images: images
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
const test = ref(false)
</script>

<template>
    <FormHeader>
        <Link :href="route('admin.products.index')" class="mr-4">
            <i class="bi bi-arrow-left-short text-2xl"></i> Powrót
        </Link>
        <button @click="save" class="button-primary">Zapisz</button>
    </FormHeader>
    <div class="form">
        <section v-if="attributes.length" class="py-10 max-w-4xl mx-auto">
            <FormField
                :required="true"
                label-for="name"
                label="Nazwa">
                <Input v-model="product.attributes.name" :required="true" id="name"/>
            </FormField>

            <FormField
                :required="true"
                label-for="sku"
                label="Sku">
                <Input v-model="product.sku" :required="true" id="sku"/>
            </FormField>

            <FormField
                :required="true"
                label-for="url_key"
                label="Klucz URL">
                <Input v-model="product.url_key" :required="true" id="url_key"/>
            </FormField>

            <FormField
                label-for="categories"
                label="Kategorie">

                <CategoryInput v-model="product.category_ids" :categories="categories" />
            </FormField>

            <FormField
                :required="true"
                label-for="price"
                label="Cena">
                <Input v-model="product.price.price" :required="true" id="price" :disabled="product.is_configurable"/>
            </FormField>

            <FormField
                label="Cena specjalna"
                label-for="special_price">
                <Input v-model="product.price.special_price" :required="true" id="special_price"/>
            </FormField>

            <FormField
                label="Cena specjalna od"
                label-for="special_price_from">
                <div class="flex items-center">
                    <DateInput v-model="product.price.special_price_from" id="special_price_from"/>
                    <div class="mx-2">do</div>
                    <DateInput v-model="product.price.special_price_to" id="special_price_to"/>
                </div>
            </FormField>

            <FormField
                :required="true"
                label="Stan magazynowy" label-for="uses_stock">

                <Toggle v-model="product.uses_stock" id="uses_stock"/>

            </FormField>

            <FormField
                v-show="product.uses_stock"
                :required="true"
                label-for="stock_qty"
                label="Ilość w magazynie">
                <Input v-model="product.stock_qty" :required="true" id="stock_qty"/>
            </FormField>

            <TextEditor v-model="product.attributes.description"/>

            <template v-for="attribute in attributes" :key="attribute.id">
                <FormField
                    v-if="attribute && attribute.code !== 'name' && attribute.code !== 'description'"
                    :required="!!attribute.is_required"
                    :label="attribute.name"
                    :label-for="'attribute-' + attribute.code"
                >

                    <AttributeInput v-model="product.attributes[attribute.code]" :attribute="attribute"/>

                </FormField>
            </template>
        </section>


        <section class="section">
            <div class="form-section-title">
                Media
            </div>
            <Gallery v-if="product" :images="product.images"/>
        </section>
    </div>
</template>

<style scoped>
</style>
