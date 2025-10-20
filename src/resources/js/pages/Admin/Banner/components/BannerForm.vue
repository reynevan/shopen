<script setup>
import {useForm} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Toggle from "@shopen/components/admin/form/input/Toggle.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import DateInput from "@shopen/components/admin/form/input/DateInput.vue";
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";
import CategoryMultiselect
    from "@shopen/components/admin/form/input/Category/CategoryMultiselect/CategoryMultiselect.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import FormField from "@shopen/components/admin/form/FormField.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import Select from "../../../../components/admin/form/input/Select.vue";


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
    category_ids: props.banner?.category_ids ?? [],
    image_desktop: null,
    image_mobile: null,
});
const imageDesktop = ref(props.banner?.image_url_desktop);
const imageMobile = ref(props.banner?.image_url_mobile);

const isEditing = computed(() => !!props.banner);

const objectToArray = (obj) => {
    return Object.entries(obj).map(([id, value]) => ({ id, value }));
}

const placementTypeOptions = objectToArray(props.placementTypes);
const placementOptions = objectToArray(props.placements);

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
    <ActionsPanel back-route="admin.banners.index">
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <section>


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
            <Select id="placement-type" :options="placementTypeOptions" v-model="form.placement_type"/>
        </FormField>

        <FormField label="Umiejscowienie" required v-if="form.placement_type === 'predefined'">
            <Select id="placement-key" :options="placementOptions" v-model="form.placement_key"/>
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

            <CategoryMultiselect v-model="form.category_ids" :categories="categories"/>
        </FormField>

        <FormField
            label="Obraz (Desktop)"
            label-for="special_price_from"
            required>
            <ImageInput @input="onDesktopFileSelect" v-if="!imageDesktop"/>
            <div class="relative" v-if="imageDesktop">
                <img :src="imageDesktop" :alt="form.alt_text">
                <button @click="removeImageDesktop"
                        class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
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
                <button @click="removeImageMobile"
                        class="bg-red-500 text-white hover:bg-red-400 transition-colors rounded-full w-8 h-8 flex items-center justify-center absolute right-2 top-2 cursor-pointer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </FormField>
    </section>
</template>
