<script setup>
import {useForm} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Select from "@shopen/components/admin/form/input/Select.vue";
import {ref} from "vue";
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";

const props = defineProps({
    brand: {
        type: Object
    }
})
const form = useForm({
    id: props.brand?.id,
    name: props.brand?.name,
    description: props.brand?.description,
    slug: props.brand?.slug,
    logo: props.brand?.logo,
    meta_title: props.brand?.meta_title,
    meta_description: props.brand?.meta_description,
    meta_keywords: props.brand?.meta_keywords,
    is_active: props.brand?.is_active,

})
const logo = ref(props.brand?.logo_url);


const save = async () => {
    if (form.id) {
        form.put(route('admin.brands.update', form.id), {
            preserveState: true,
            preserveScroll: true
        })
    } else {
        form.post(route('admin.brands.store'), {
            preserveState: true,
            preserveScroll: true
        })
    }
}

const onLogoSelect = (event) => {
    form.logo = event.target.files[0];
    logo.value = previewImage(event);
    form.remove_image = false;
}

const removeLogo = () => {
    form.logo = null;
    logo.value = null;
    if (props.brand.logo_url) {
        form.remove_logo = true;
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
    <ActionsPanel back-route="admin.brands.index">
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div>

        <FormField
            label="Aktywna" label-for="is_active">
            <Toggle v-model="form.is_active" id="is_active"/>
        </FormField>

        <FormField
            :required="true"
            :error="form.errors.name"
            label="Nazwa" label-for="name">
            <Input v-model="form.name" id="name"/>
        </FormField>

        <FormField
            :required="true"
            :error="form.errors.slug"
            label="Klucz URL" label-for="slug">
            <Input v-model="form.slug" id="slug"/>
        </FormField>

        <FormField
            label="Logo"
            label-for="logo">
            <ImageInput @input="onLogoSelect" v-if="!logo" id="logo"/>
            <div class="relative" v-if="logo">
                <img :src="logo" class="max-h-[200px]">
                <button @click="removeLogo" class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </FormField>

        <FormField
            :error="form.errors.meta_title"
            label="Tytuł SEO" label-for="meta_title">
            <Input v-model="form.meta_title" id="meta_title"/>
        </FormField>

        <FormField
            :error="form.errors.meta_description"
            label="Opis SEO" label-for="meta_description">
            <Input v-model="form.meta_description" id="meta_description"/>
        </FormField>

    </div>
</template>