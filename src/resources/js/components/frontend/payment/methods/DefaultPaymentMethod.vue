<script setup>

import IconCircle from "@shopen/components/icons/IconCircle.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import {usePaymentStore} from "@shopen/stores/payment.js";

const payment = usePaymentStore();

const props = defineProps(['method']);
const page = usePage();

const isSelected = computed(() => page.props.selectedPaymentMethod === props.method.key);

const selectMethod = () => {
    payment.selectMethod(props.method.key)
}
</script>

<template>
<div>
    <div
         class="flex justify-between items-center px-4 py-2 mb-2 cursor-pointer rounded transition-colors hover:bg-accent/10 border"
         :class="[isSelected ? 'bg-accent/10 border-strong': 'border-transparent']"
         @click="selectMethod">
        <div class="flex">
            <div class="pt-1 mr-2 text-neutral-700">
                <IconCheckCircle v-if="isSelected"/>
                <IconCircle v-else></IconCircle>
            </div>
            <div>
                <div class="font-semibold">{{ method.name }}</div>
                <div class="text-neutral-500" v-if="method.description">{{ method.description }}</div>
            </div>
        </div>
        <div class="w-20 text-right" v-if="method.price">
            {{ method.price }}
        </div>
        <slot name="default"/>
    </div>
</div>
</template>

<style scoped>

</style>