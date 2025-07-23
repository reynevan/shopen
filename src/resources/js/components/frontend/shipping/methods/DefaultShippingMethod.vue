<script setup>

import {useShippingStore} from "@shopen/stores/shipping.js";
import IconCircle from "@shopen/components/icons/IconCircle.vue";
import IconCircleCheck from "@shopen/components/icons/IconCircleCheck.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const shipping = useShippingStore();

const props = defineProps(['method']);
const page = usePage();

const isSelected = computed(() => page.props.selectedShippingMethod === props.method.key);

const selectMethod = () => {
    page.props.selectedShippingMethod = props.method.key;
    shipping.selectMethod(props.method.key);
}
</script>

<template>
<div>
    <div
         class="flex justify-between items-center px-4 py-2 mb-2 cursor-pointer rounded transition-colors hover:bg-accent/10 border"
         :class="{'bg-accent/10 border-accent': isSelected, 'border-transparent':  !isSelected}"
         @click="selectMethod">
        <div class="flex">
            <div class="pt-1 mr-2 text-accent">
                <IconCircleCheck v-if="isSelected"></IconCircleCheck>
                <IconCircle v-else></IconCircle>
            </div>
            <div>
                <div class="font-semibold">{{ method.name }}</div>
                <div class="text-neutral-500" v-if="method.description">{{ method.description }}</div>
            </div>
        </div>
        <div class="w-20 text-right">
            {{ method.price }}
        </div>
        <slot name="default"/>
    </div>
</div>
</template>

<style scoped>

</style>