<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import FormField from "../form/FormField.vue";
import Toggle from "../form/input/Toggle.vue";
import FormHeader from "../form/FormHeader.vue";
import Input from "../form/input/Input.vue";
import DateInput from "../form/input/DateInput.vue";
import ImageInput from "../form/input/ImageInput.vue";
import CategoryMultiselect from "@shopen/components/admin/form/input/Category/CategoryMultiselect/CategoryMultiselect.vue";


const props = defineProps({
    banner: {
        type: Object,
        default: null,
    },
    placementTypes: Object,
    placements: Object,
    categories: Array,
});

const form = useForm({
    _method: props.banner ? 'PUT' : 'POST',
    title: props.banner?.title ?? '',
    alt_text: props.banner?.alt_text ?? '',
    link_url: props.banner?.link_url ?? '',
    opens_in_new_tab: props.banner?.opens_in_new_tab ?? true,
    placement_type: props.banner?.placement_type ?? 'predefined',
    placement_key: props.banner?.placement_key ?? '',
    start_date: props.banner?.start_date?.split(' ')[0] ?? null,
    end_date: props.banner?.end_date?.split(' ')[0] ?? null,
    is_active: props.banner?.is_active ?? true,
    sort_order: props.banner?.sort_order ?? 0,
    category_ids: props.banner?.categories?.map(c => c.id) ?? [],
    image_desktop: null,
    image_mobile: null,
});
const imageDesktop = ref(props.banner?.image_url_desktop);
const imageMobile = ref(props.banner?.image_url_mobile);

const isEditing = computed(() => !!props.banner);

const save = () => {
    const url = isEditing.value
        ? route('admin.banners.update', props.banner.id)
        : route('admin.banners.store');

    form.post(url, {
        onError: (errors) => console.log(errors),
        onSuccess: () => form.reset('image_desktop', 'image_mobile'),
    });
};

const onDesktopFileSelect = (event) => {
    form.image_desktop = event.target.files[0];
    imageDesktop.value = previewImage(event);
}

const removeImageDesktop = () => {
    form.image_desktop = null;
    imageDesktop.value = null;
}

const onMobileFileSelect = (event) => {
    form.image_mobile = event.target.files[0];
    imageMobile.value = previewImage(event);
}

const removeImageMobile = () => {
    form.image_mobile = null;
    imageMobile.value = null;
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
        <Link :href="route('admin.banners.index')" class="mr-4">
            <i class="bi bi-arrow-left-short text-2xl"></i> Powrót
        </Link>
        <button @click="save" class="button-primary" :disabled="form.processing">Zapisz</button>
    </FormHeader>
    <div class="form">
        <section class="py-10 max-w-4xl mx-auto">


            <FormField label="Aktywny" required>
                <Toggle class="pt-2" v-model="form.is_active"/>
            </FormField>
            <FormField label="Tytuł" required>
                <Input v-model="form.title" required/>
            </FormField>

            <FormField label="Alt tekst">
                <Input v-model="form.alt_text"/>
            </FormField>

            <FormField label="Kolejność sortowania">
                <Input v-model="form.sort_order"/>
            </FormField>

            <FormField label="Link URL">
                <Input v-model="form.link_url"/>
            </FormField>

            <FormField label="Otwórz w nowej karcie">
                <Toggle class="pt-2" v-model="form.opens_in_new_tab"/>
            </FormField>

            <FormField label="Typ umiejscowienia" required>
                <select id="discount_type" v-model="form.placement_type">
                    <option v-for="(value, key) in placementTypes" :key="key" :value="key">
                        {{ value }}
                    </option>
                </select>
            </FormField>

            <FormField label="Umiejscowienie" required v-if="form.placement_type === 'predefined'">
                <select id="placement_key" v-model="form.placement_key">
                    <option v-for="(value, key) in placements" :key="key" :value="key">
                        {{ value }}
                    </option>
                </select>
            </FormField>

            <FormField label="Identyfikator" v-if="form.placement_type === 'dynamic'">
                <Input v-model="form.placement_key"/>
            </FormField>

            <FormField
                label="Aktywny od"
                label-for="special_price_from">
                <div class="flex items-center">
                    <DateInput v-model="form.start_date" id="start_date"/>
                    <div class="mx-2">do</div>
                    <DateInput v-model="form.end_date" id="end_date"/>
                </div>
            </FormField>

            <FormField
                v-show="form.placement_key && (form.placement_key.startsWith('category_') || form.placement_key.startsWith('product_'))"
                label-for="categories"
                label="Kategorie">

                <CategoryMultiselect v-model="form.category_ids" :categories="categories" />
            </FormField>

            <FormField
                label="Obraz (Desktop)"
                label-for="special_price_from"
                required>
                <ImageInput @input="onDesktopFileSelect" v-if="!imageDesktop"/>
                <div class="relative" v-if="imageDesktop">
                    <img :src="imageDesktop" :alt="form.alt_text">
                    <button @click="removeImageDesktop" class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </FormField>

            <FormField
                label="Obraz (Mobile)"
                label-for="special_price_from">
                <ImageInput @input="onMobileFileSelect" v-if="!imageMobile"/>
                <div class="relative" v-if="imageMobile">
                    <img :src="imageMobile" :alt="form.alt_text">
                    <button @click="removeImageMobile" class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </FormField>
        </section>
    </div>
</template>
