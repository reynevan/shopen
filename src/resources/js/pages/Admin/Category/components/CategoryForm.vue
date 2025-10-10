<script setup>

import CategoriesTree from "@shopen/pages/Admin/Category/components/CategoriesTree/CategoriesTree.vue";
import {useCategoryStore} from "@shopen/stores/admin/categoryStore.js";
import AttributeInput from "@shopen/components/admin/form/input/AttributeInput.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import {router, useForm} from "@inertiajs/vue3";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";
import {onBeforeUnmount, ref, watch} from "vue";
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import CategorySelect from "@shopen/components/admin/form/input/Category/CategorySelect/CategorySelect.vue";

const props = defineProps(['categories', 'category', 'attributes'])

const categoryStore = useCategoryStore()

const initialFormState = (category) => ({
    attributes: Array.isArray(category?.attributes) ? {} : (category?.attributes ?? {}),
    is_active: category?.is_active ?? false,
    parent_id: category?.parent_id,
    seo: Array.isArray(category?.seo) ? {} : (category?.seo ?? {}),
    image_desktop: null,
    image_mobile: null,
    image_menu: null,
    remove_image_desktop: false,
    remove_image_mobile: false,
    remove_image_menu: false
})
const form = useForm(initialFormState(props.category))

const imageMenu = ref(props.category?.menu_image_url)

watch(() => props.category, (newCategory) => {
    form.defaults({...initialFormState(newCategory)})
    form.reset()

    form.clearErrors();
    imageMenu.value = newCategory?.menu_image_url
}, { deep: true })



const save = () => {
    if (props.category?.id) {
        form.put(route('admin.categories.update', props.category.id), {})
    } else {
        form.post(route('admin.categories.store'), {})
    }
}

const onMenuFileSelect = (event) => {
    form.image_menu= event.target.files[0];
    imageMenu.value = previewImage(event)
    form.remove_image_menu = false
}

const removeImageMenu = () => {
    form.image_menu = null
    imageMenu.value = null
    if (props.category.menu_image_url) {
        form.remove_image_menu = true
    }
}

const previewImage = (event) => {
    const file = event.target.files[0]
    if (file) {
        return URL.createObjectURL(file)
    }
    return null;
}

const addNew = () => {
    router.get(route('admin.categories.create'))
}

const expandAll = () => {
    categoryStore.expandAll()
}

const collapseAll = () => {
    categoryStore.collapseAll()
}

onBeforeUnmount(() => {
    categoryStore.reset()
})
</script>

<template>
    <ActionsPanel>
        <template #title v-if="category?.attributes?.name">{{ category.attributes.name }}</template>
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>

    <div class="form">
        <div class="section py-10">
            <div class="flex">
                <div class="max-w-lg w-full">
                    <div class="flex border-b pb-4 mb-4">
                        <Button type="primary" @click="addNew">
                            <i class="bi bi-plus-lg"></i>
                            Nowa kategoria
                        </Button>
                        <div class="flex divide-x ml-2">
                            <ActionButton type="down" @click="expandAll">Rozwiń wszystkie</ActionButton>
                            <ActionButton type="next" @click="collapseAll">Zwiń wszystkie</ActionButton>
                        </div>
                    </div>
                    <CategoriesTree :categories="categories"
                    />
                </div>

                <div class="pl-6 ml-6 border-l w-full">

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
                        label-for="is_active"
                        label="Aktywna">
                        <Toggle v-model="form.is_active" id="is_active"/>
                    </FormField>

                    <FormField
                        label-for="parent_id"
                        label="Kategoria nadrzędna">
                        <CategorySelect :categories="categories" v-model="form.parent_id"/>
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
                        label-for="seo_title"
                        label="Tytuł SEO">
                        <Input v-model="form.seo.seo_title" id="seo_title"/>
                    </FormField>

                    <FormField
                        label-for="seo_description"
                        label="Opis SEO">
                        <Input v-model="form.seo.seo_description" id="seo_description"/>
                    </FormField>

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
            </div>
        </div>
    </div>
</template>