<script setup>

import {computed, onMounted, ref} from "vue";
import {useShippingStore} from "@shopen/stores/shipping.js"
import IconCircle from "@shopen/components/icons/IconCircle.vue"
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue"
import {usePage} from "@inertiajs/vue3";
import {useGeoWidgetStore} from "@shopen/stores/geoWidget.js";

const shipping = useShippingStore()
const geoWidget = useGeoWidgetStore();

geoWidget.onPointSelect((point) => {
    selectedPoint.value = point.detail;
    shipping.selectMethod(props.method.key, point.detail);
    closeMap()
})

const props = defineProps(['method']);
const page = usePage();
const isSelected = computed(() => page.props.selectedShippingMethod === props.method.key);

const selectedPoint = ref(page.props.deliveryPoint);

const onClick = () => {
    if (selectedPoint.value) {
        shipping.selectMethod(props.method.key, selectedPoint.value);
    } else {
        openMap();
    }
}
const openMap = () => {
    geoWidget.open();
}
const closeMap = () => {
    geoWidget.close();
}
</script>

<template>
        <div
            class="flex justify-between items-center px-4 py-2 mb-2 cursor-pointer rounded transition-colors hover:bg-accent/10 border w-full"
            :class="[isSelected ? 'bg-accent/10': 'border-light']"
            @click="onClick">
            <div class="flex">
                <div class="pt-1 mr-2 text-neutral-700">
                    <IconCheckCircle v-if="isSelected"/>
                    <IconCircle v-else></IconCircle>
                </div>
                <div>
                    <div class="font-semibold">{{ method.name }}</div>
                    <div class="text-neutral-500" v-if="method.description">{{ method.description }}</div>
                    <div v-if="selectedPoint && isSelected">
                        {{ selectedPoint.name }} - <a @click="openMap" class="text-accent cursor-pointer">Zmień</a>
                    </div>
                    <div v-if="selectedPoint && isSelected" class="text-sm">
                        <div v-if="selectedPoint.address.line1" class="font-semibold">
                            {{ selectedPoint.address.line1 }}
                        </div>
                        <div v-if="selectedPoint.address.line2">
                            {{ selectedPoint.address.line2 }}
                        </div>
                        <div v-if="selectedPoint.location_description">
                            {{ selectedPoint.location_description }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-20 text-right">
                {{ method.price }}
            </div>
        </div>
</template>
