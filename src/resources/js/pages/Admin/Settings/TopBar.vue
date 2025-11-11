<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import FormField from "../../../components/admin/form/FormField.vue";
import {useForm} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import TextEditor from "@shopen/components/admin/form/input/TextEditor.vue";
import ColorPicker from "@shopen/components/admin/form/input/ColorPicker.vue";
import Button from "../../../components/frontend/ui/Button.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    slides: {type: Array}
})

const form = useForm({
    slides: props.slides,
})

const addSlide = () => {
    form.slides.push({
        content: '',
        color: '#000',
        background_color: '#fff'
    })
}

const removeSlide = (index) => {
    form.slides.splice(index, 1)
}

const save = () => {
    form.put(route('admin.settings.top-bar.update'))
}
</script>

<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Ustawienia</PageTitle>
        </template>
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <section>
        <div class="space-y-8">
            <div v-for="(slide, index) in form.slides" :key="index" class="flex items-start border-b border-dark">
                <div class="w-full">
                    <FormField label="Treść">
                        <TextEditor v-model="slide.content" :id="'slide-' + index" :rows="1"/>
                    </FormField>
                    <FormField label="Kolor tekstu" field="color">
                        <ColorPicker v-model="slide.color"/>
                    </FormField>
                    <FormField label="Kolor tła" field="background_color">
                        <ColorPicker v-model="slide.background_color"/>
                    </FormField>
                    <FormField label="Podgląd">
                        <div class="flex w-full items-center justify-center px-4 min-h-[40px]" :style="{color: slide.color, 'background-color': slide.background_color}" v-html="slide.content"></div>
                    </FormField>
                </div>
                <div class="pl-4">
                    <ActionButton type="remove" size="lg" @click="removeSlide(index)"/>
                </div>
            </div>
        </div>
        <ActionButton type="add" @click="addSlide">Dodaj</ActionButton>
    </section>
</template>