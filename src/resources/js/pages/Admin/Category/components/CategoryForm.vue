<script setup>

import CategoriesTree from "@shopen/pages/Admin/Category/components/CategoriesTree/CategoriesTree.vue";
import {useCategoryStore} from "@shopen/stores/admin/categoryStore.js";
import AttributeInput from "@shopen/components/admin/form/input/AttributeInput.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import FormHeader from "@shopen/components/admin/form/FormHeader.vue";
import { useForm} from "@inertiajs/vue3";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";
import {ref} from "vue";
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";
import Toggle from "../../../../components/admin/form/input/Toggle.vue";

const props = defineProps(['categories', 'category', 'attributes'])

const categoryStore = useCategoryStore();
const form = useForm({
    _method: props.category ? 'PUT' : 'POST',
    attributes: props.category?.attributes ?? [],
    is_active: props.category?.is_active ?? false,
    seo: props.category?.seo ?? [],
    image_desktop: null,
    image_mobile: null,
    image_menu: null,
    remove_image_desktop: false,
    remove_image_mobile: false,
    remove_image_menu: false
});

const imageMenu = ref(props.category?.menu_image_url);

function simplifyArrayRecursive(data) {
    return data.map(item => ({
        id: item.id,
        children: item.children && item.children.length > 0
            ? simplifyArrayRecursive(item.children)
            : []
    }));
}

const onMoveCategory = (_categories) => {
    categoryStore.move(simplifyArrayRecursive(_categories));
}

const save = () => {
    form.post(route('admin.categories.update', props.category.id), {})
}

const onMenuFileSelect = (event) => {
    form.image_menu= event.target.files[0];
    imageMenu.value = previewImage(event);
    form.remove_image_menu = false;
}

const removeImageMenu = () => {
    form.image_menu = null;
    imageMenu.value = null;
    if (props.category.menu_image_url) {
        form.remove_image_menu = true;
    }
}

const previewImage = (event) => {
    const file = event.target.files[0];
    if (file) {
        return URL.createObjectURL(file);
    }
    return null;
};

</script>

<template>
    <FormHeader>
        <button @click="save" class="button-primary">Zapisz</button>
    </FormHeader>
    <div class="form">
        <div class="section py-10">
            <div class="flex">
                <div>
                    <CategoriesTree
                        :categories="categories"
                        @move:category="onMoveCategory"
                    />
                </div>
                <div>

                    <FormField
                        :required="true"
                        label-for="name"
                        label="Nazwa">
                        <Input v-model="form.attributes.name" :required="true" id="name"/>
                    </FormField>

                    <FormField
                        label-for="display_in_menu"
                        label="Pokaż w menu">
                        <Toggle v-model="form.attributes.display_in_menu" id="display_in_menu"/>
                    </FormField>

                    <FormField
                        label-for="description"
                        label="Opis">
                        <TextEditor v-model="form.attributes.description"/>
                    </FormField>

                    <FormField
                        label="Obraz (Menu)"
                        label-for="image_menu">
                        <ImageInput @input="onMenuFileSelect" v-if="!imageMenu" id="image_menu"/>
                        <div class="relative" v-if="imageMenu">
                            <img :src="imageMenu">
                            <button @click="removeImageMenu" class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </FormField>

                    <FormField
                        :required="true"
                        label-for="seo_title"
                        label="Tytuł SEO">
                        <Input v-model="form.seo.seo_title" :required="false" id="seo_title"/>
                    </FormField>

                    <FormField
                        :required="true"
                        label-for="seo_description"
                        label="Opis SEO">
                        <Input v-model="form.seo.seo_description" :required="false" id="seo_description"/>
                    </FormField>

                    <template v-for="attribute in attributes" :key="attribute.id">
                        <FormField
                            v-if="attribute && !attribute.is_system"
                            :required="attribute.is_required"
                            :label="attribute.name"
                            :label-for="'attribute-' + attribute.code"
                        >
                            <AttributeInput v-model="form.attributes[attribute.code]"
                                            :attribute="attribute"/>

                        </FormField>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>