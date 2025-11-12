<script setup>
import {ref} from "vue";
import draggable from 'vuedraggable'
import GalleryItem from "./GalleryItem.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import Input from "@shopen/components/admin/form/input/Input.vue";
import {useBodyScrollLock} from "../../../../../../composables/useBodyScrollLock";
import MediaTypeTag from "./MediaTypeTag.vue";


const images = defineModel('images');
const drag = ref(false);
const selectedMedia = ref(null);

const bodyScrollLock = useBodyScrollLock()

const removeImage = (index) => {
    images.value.splice(index, 1);
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
const addMedia = () => {
    let media = {
        id: 'new-' + images.value.length,
        new: true,
        gallery: true,
        order: images.value.length + 1,
    };
    if (!images.value || !images.value.length) {
        media.thumbnail = true;
    }
    images.value.push(media);
}
const toggleType = (image, type) => {
    image[type] = !image[type];
}

const selectMedia = (media) => {
    bodyScrollLock.lock()
    selectedMedia.value = media;
}
const closeMediaDetails = () => {
    bodyScrollLock.unlock()
    selectedMedia.value = null;
}
</script>

<template>
    <div>
        <div class="w-1/2">
            <Button @click="addMedia">Dodaj <i class="bi bi-plus"></i></Button>
            <div class="mt-4">
                <draggable
                    :list="images"
                    class="flex gap-4"
                    @update:list="val => images.value = val"
                    @start="onStartDrag"
                    @end="onEndDrag"
                    item-key="id">
                    <template #item="{element, index}">
                        <GalleryItem v-model="images[index]" @onSelect="selectMedia($event)" @onRemove="removeImage(index)"/>
                    </template>
                </draggable>
            </div>
        </div>
        <div class="fixed z-[200] top-0 left-0 bottom-0 right-0 transition-all duration-300 bg-black/50"
             v-if="selectedMedia"
             @click="closeMediaDetails"></div>
        <div class="fixed z-[200] top-0 left-[200px] bottom-0 right-0 transition-all duration-300 bg-white overflow-y-auto"
             :class="selectedMedia ? '' : 'translate-x-full'">
            <div v-if="selectedMedia" class="flex ">
                <div class="w-full pl-6 py-6 h-full">
                    <div class="flex justify-center border border-light">
                        <img :src="selectedMedia.url" class="max-h-full">
                    </div>
                </div>
                <div class="w-full py-6 pl-6 pr-16 relative">
                    <div class="absolute right-2 top-2">
                        <Button @click="closeMediaDetails" type="ghost" size="sm">
                            <i class="bi bi-x-lg text-xl"></i>
                        </Button>
                    </div>
                    <div class="mb-4">
                        <label class="mb-2 font-semibold">Alt text</label>
                        <Input v-model="selectedMedia.alt"/>
                    </div>
                    <div class="section mb-4">
                        <label class="mb-2 font-semibold">Role</label>
                        <div class="flex items-center divide-x">
                            <MediaTypeTag name="Galeria" :selected="selectedMedia.gallery" @onClick="toggleType(selectedMedia, 'gallery')"/>
                            <MediaTypeTag name="Thumbnail" :selected="selectedMedia.thumbnail" @onClick="toggleType(selectedMedia, 'thumbnail')"/>
                            <MediaTypeTag name="Meta" :selected="selectedMedia.meta" @onClick="toggleType(selectedMedia, 'meta')"/>
                        </div>
                    </div>
                    <div class="section">
                        <div class="mb-4">
                            <label class="font-semibold">Rozmiar</label>
                            <p>{{ selectedMedia.size }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="font-semibold">Wymiary</label>
                            <p>{{ selectedMedia.width }}x{{ selectedMedia.height }} px</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>