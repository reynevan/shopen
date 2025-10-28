<script setup>

import {useShippingStore} from "@shopen/stores/shipping.js";
import IconCircle from "@shopen/components/icons/IconCircle.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
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
    <div
         class="shipping-method flex justify-between items-center w-full"
         :class="[isSelected ? 'selected': '']"
         @click="selectMethod">
        <div class="flex">
            <div class="pt-1 mr-2 text-neutral-700">
                <IconCheckCircle v-if="isSelected"/>
                <IconCircle v-else></IconCircle>
            </div>
            <div>
                <div class="shipping-method-name">{{ method.name }}</div>
                <div class="shipping-method-description" v-if="method.description">{{ method.description }}</div>
            </div>
        </div>
        <div class="w-20 text-right shipping-method-price">
            {{ method.price }}
        </div>
        <slot name="default"/>
    </div>
</template>