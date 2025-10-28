<script setup>
import IconX from "@shopen/components/icons/IconX.vue";
import {useGeoWidgetStore} from "@shopen/stores/geoWidget.js";
import {usePage} from "@inertiajs/vue3";
import {onMounted} from "vue";

const geoWidget = useGeoWidgetStore();
const page = usePage();

onMounted(() => {
    const src = 'https://geowidget.inpost.pl/inpost-geowidget.js'
    const existing = document.querySelector(`script[src="${src}"]`)
    if (!existing) {
        const geoWidgetScript = document.createElement('script')
        geoWidgetScript.setAttribute('src', src)
        document.head.appendChild(geoWidgetScript)
        document.addEventListener('onpointselect', geoWidget.selectPoint)
    }
})
</script>

<template>
    <Teleport to="body">
        <div class="fixed z-[1000] top-0 left-0 bottom-0 right-0 bg-black/80" v-show="geoWidget.opened">
            <div class="absolute top-2 right-2 cursor-pointer text-white" @click="geoWidget.close()">
                <IconX size="2xl"/>
            </div>
            <div class="p-10 h-full">
                <inpost-geowidget onpoint="onpointselect" :token="page.props.geoWidgetToken" language="pl" config="parcelcollect"></inpost-geowidget>
            </div>
        </div>
    </Teleport>
</template>