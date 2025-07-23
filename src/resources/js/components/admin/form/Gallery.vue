<script setup>
import {ref} from "vue";
import ImageInput from "./input/ImageInput.vue";
import Loader from "../../Loader.vue";
import draggable from 'vuedraggable'
import axios from "axios";


const images = defineModel('images');
const drag = ref(false);

const randomId = () => {
    let id = Math.random().toString(36).substring(2, 10);
    while (images.value.map((el) => { return el.index }).indexOf(id) >= 0) {
        id = Math.random().toString(36).substring(2, 10);
    }
    return id;
}

const onSelect = (file) => {
    let index = randomId();

    let image = {
        index: index,
        loading: true,
        order: images.value.length + 1
    };

    let data = new FormData();
    data.append('file', file);
    axios.post('/admin/api/upload-image', data)
        .then(response => {
            for (let i = 0; i < images.value.length; i++) {
                if (images.value[i].index === index) {
                    images.value[i].url = response.data.location;
                    images.value[i].path = response.data.path;
                    images.value[i].loading = false;
                }
            }
        })
        .finally(() => {
            image.loading = false;
        })
    images.value.push(image);
}
const removeImage = (image) => {
    for (let i = 0; i < images.value.length; i++) {
        if ((images.value[i].id && images.value[i].id === image.id) || (images.value[i].index && images.value[i].index === image.index)) {
            images.value.splice(i, 1);
        }
    }
}
const onStartDrag = () => {
    drag.value = true;
}
const onEndDrag = () => {
    drag.value = false;
    for (let i = 0; i < images.value.length; i++) {
        images.value[i].order = i;
    }
}
</script>

<template>
    <div class="flex flex-wrap">
        <draggable
            class="flex"
            v-model="images"
            @start="onStartDrag"
            @end="onEndDrag"
            item-key="index">
            <template #item="{element, index}">
                <div class="m-2 flex justify-center images-center rounded-lg border border-dashed border-gray-900/25 hover:border-gray-900/35 transition-border relative w-[180px] h-[180px]">
                    <img v-if="!element.loading"
                        :src="element.url"
                        class="object-contain"
                        alt=""
                    />
                    <div v-if="element.loading">
                        <Loader :loading="true"/>
                    </div>
                    <button @click="removeImage(element)" class="text-neutral-400 absolute bottom-2 left-2 z-50"><i class="bi bi-trash3"></i></button>
                </div>
            </template>
        </draggable>
        <ImageInput @selected="onSelect"/>
    </div>
</template>