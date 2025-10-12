<script setup>
import FormField from "@shopen/components/admin/form/FormField.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import Gallery from "@shopen/pages/Admin/Product/components/ProductForm/Gallery/Gallery.vue";
import AttributeInput from "@shopen/components/admin/form/input/AttributeInput.vue";
import {ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import GeneralSection from "@shopen/pages/Admin/Product/components/ProductForm/FormSections/GeneralSection.vue";
import RelatedProductsSection from "@shopen/pages/Admin/Product/components/ProductForm/FormSections/RelatedProductsSection.vue";
import PriceSection from "@shopen/pages/Admin/Product/components/ProductForm/FormSections/PriceSection.vue";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";
import SectionTitle from "./SectionTitle.vue";
import FormMenu from "@shopen/components/admin/form/menu/FormMenu.vue";
import ConfigurationsSection from "./FormSections/ConfigurationsSection.vue";

const page = usePage();
const props = defineProps({
    product: {
        type: Object,
        required: false
    },
    parent: {
        type: Object,
        required: false
    },
    variants: {
        type: Object,
        required: false
    },
    categories: {
        type: Array
    },
    attributes: {
        type: Array,
        default: []
    },
    brands: {
        type: Array,
        default: []
    }
});
const defaultPrice = {
    price: null,
    special_price: null
};
const defaultAttributesValues = {};
props.attributes.forEach((attr) => {
    defaultAttributesValues[attr.code] = null;
})
const form = useForm({
    attributes: props.product.id ? props.product.attributes : defaultAttributesValues,
    type: props.product?.type ?? 'simple',
    visibility: props.product?.visibility ?? 3,
    parent_id: props.parent?.id,
    configurable_attributes: props.product?.configurable_attributes ?? [],
    sku: props.product?.sku,
    ean: props.product?.ean,
    url_key: props.product?.url_key,
    category_ids: props.product?.category_ids ?? [],
    price: props.product?.price ?? defaultPrice,
    related_products_ids: props.product?.related_products_ids ?? [],
    up_sell_ids: props.product?.up_sell_ids ?? [],
    cross_sell_ids: props.product?.cross_sell_ids ?? [],
    images: props.product?.images ?? [],
    uses_stock: props.product?.uses_stock ?? 0,
    stock_qty: props.product?.stock_qty ?? 0,
    brand_id: props.product?.brand_id,
    is_virtual: props.product?.is_virtual,
})

const brandsOptions = props.brands.map(brand => {return {id: brand.id, value: brand.name}})

const save = async () => {
    if (props.product.id) {
        form.put(route('admin.products.update', props.product.id), {
            preserveState: true,
            preserveScroll: true
        });
    } else {
        form.post(route('admin.products.store'), {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const activeSection = ref('general');
const onChangeSection = (section) => {
    activeSection.value = section;
}
const sections = [
    {
        section: 'general',
        title: 'Główne'
    },
    {
        section: 'description',
        title: 'Opis'
    },
    {
        section: 'price',
        title: 'Cena',
        disabled: () => form.type === 'configurable'
    },
    {
        section: 'related_products',
        title: 'Produkty powiązane'
    },
    {
        section: 'attributes',
        title: 'Atrybuty'
    },
    {
        section: 'media',
        title: 'Media'
    }
]

if (props.product.is_configurable && props.product.id) {
    sections.push({
        section: 'configurations',
        title: 'Konfiguracje'
    })
}

</script>

<template>
    <ActionsPanel back-route="admin.products.index">
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <div v-if="Object.keys(page.props.errors).length > 0"
         class="bg-red-100 text-red-800 px-6 py-4 mb-6">
        <div v-for="error in page.props.errors">
            {{ error}}
        </div>
    </div>
    <div class="flex items-start gap-6">
        <div class="sticky top-20">
            <FormMenu :sections="sections" @onSelect="onChangeSection"/>
        </div>
        <div class="border-l border-light pl-6 w-full">
            <div v-show="activeSection === 'general'">
                <SectionTitle>{{ product?.id ? product.attributes.name : 'Nowy produkt' }}</SectionTitle>
                <GeneralSection
                    v-model="form"
                    :product="product"
                    :parent="parent"
                    :categories="categories"
                    :attributes="attributes"
                    :brands="brandsOptions"/>
            </div>

            <div v-show="activeSection === 'price'">
                <SectionTitle>Cena</SectionTitle>
                <PriceSection :form="form" :product="product"/>
            </div>

            <div v-if="product && product.id && product.is_configurable" v-show="activeSection === 'configurations'">
                <SectionTitle>Konfiguracje</SectionTitle>
                <ConfigurationsSection :product="product" :variants="variants" :attributes="attributes"/>
            </div>

            <div v-show="activeSection === 'attributes'">
                <SectionTitle>Atrybuty</SectionTitle>
                <template v-for="attribute in attributes" :key="attribute.id">
                    <FormField
                        v-if="attribute && !attribute.is_system"
                        :label="attribute.name"
                        :label-for="'attribute-' + attribute.code"
                    >

                        <AttributeInput v-model="form.attributes[attribute.code]" :attribute="attribute"/>

                    </FormField>
                </template>
            </div>

            <div v-show="activeSection === 'description'">
                <SectionTitle>Opis</SectionTitle>
                <FormField
                    label-for="description"
                    label="Opis">
                    <TextEditor v-model="form.attributes.description"/>
                </FormField>
            </div>

            <div v-show="activeSection === 'related_products'">
                <SectionTitle>Produkty powiązane</SectionTitle>
                <RelatedProductsSection
                    @update:related="product.related_products = $event"
                    @update:upSell="product.up_sell_products = $event"
                    @update:crossSell="product.cross_sell_products = $event"
                    :product="product"
                    :form="form"/>
            </div>

            <section v-show="activeSection === 'media'">
                <SectionTitle>Media</SectionTitle>
                <Gallery v-if="product" :images="form.images"/>
            </section>
        </div>
    </div>


</template>
