<script setup>
import {useForm} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import {ref} from "vue";
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";
import PageTitle from "../../../../components/admin/ui/PageTitle.vue";

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
    seo_title: props.brand?.seo?.seo_title,
    seo_description: props.brand?.seo?.seo_description,
    is_active: props.brand?.is_active ?? false,
    show_on_homepage: props.brand?.show_on_homepage ?? false,

})
const logo = ref(props.brand?.logo_url);


const save = async () => {
    if (form.id) {
        form.post(route('admin.brands.update', form.id), {
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
        <template #title v-if="brand.id">
            <PageTitle>{{ brand.name }}</PageTitle>
        </template>
        <Button @click="save" class="button-primary">Zapisz</Button>
    </ActionsPanel>
    <div>

        <FormField
            label="Aktywna" label-for="is_active">
            <Toggle v-model="form.is_active" id="is_active"/>
        </FormField>

        <FormField label="Pokaż na stronie głównej">
            <Toggle class="pt-2" v-model="form.show_on_homepage"/>
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
            <div class="flex items-top gap-2" v-if="logo">
                <img :src="logo" class="max-h-[200px]">
                <button @click="removeLogo" class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </FormField>

        <FormField
            :error="form.errors.seo_title"
            label="Tytuł SEO" label-for="seo_title">
            <Input v-model="form.seo_title" id="seo_title"/>
        </FormField>

        <FormField
            :error="form.errors.seo_description"
            label="Opis SEO" label-for="seo_description">
            <Input v-model="form.seo_description" id="seo_description"/>
        </FormField>

    </div>
</template>