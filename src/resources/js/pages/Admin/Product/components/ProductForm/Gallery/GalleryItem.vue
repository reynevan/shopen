<script setup>
import ImageInput from "@shopen/components/admin/form/input/ImageInput.vue";
import axios from "axios";
import {ref} from "vue";

const model = defineModel();
const emits = defineEmits(['onSelect', 'onRemove'])

const loading = ref(false)

const onSelect = (file) => {

    loading.value = true;

    let data = new FormData();
    data.append('file', file);

    axios.post('/admin/api/upload-image', data)
        .then(response => {
            model.value.url = response.data.location;
            model.value.path = response.data.path;
            model.value.width  = response.data.width;
            model.value.height  = response.data.height;
            model.value.size  = response.data.size;
        })
        .finally(() => {
            loading.value = false;
        })
}

</script>

<template>
    <div>
        <div class="flex justify-end w-full mb-1">
            <div class="text-xs flex items-center gap-1 cursor-pointer hover:bg-accent px-2 transition-all duration-300"
                 @click.prevent="emits('onRemove')">
                <i class="bi bi-x-lg"></i> USUŃ
            </div>
        </div>
        <div v-if="!model.url">
            <ImageInput @selected="onSelect"/>
        </div>
        <div v-else @click.prevent="emits('onSelect', model)">
            <div class="border mb-2 w-[150px] h-[150px] flex items-center justify-center cursor-pointer relative group">
                <img :src="model.url" class="max-w-[150px] max-h-[150px] opacity-100 group-hover:opacity-70 transition-all"/>

            </div>
            <div class="text-xs text-neutral-500 mb-1">
                {{ model.width }}x{{ model.height }} px | {{ model.size }}
            </div>
            <div class="flex items-center gap-2 text-neutral-600">
                <div v-if="model.thumbnail" class="px-1 text-xs bg-accent border border-light">Thumbnail</div>
                <div v-if="model.gallery" class="px-1 text-xs bg-accent border border-light">Galeria</div>
                <div v-if="model.meta" class="px-1 text-xs bg-accent border border-light">Meta</div>
            </div>
        </div>
    </div>
</template>