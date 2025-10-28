<script setup>

import DefaultShippingMethod from "@shopen/components/frontend/shipping/methods/DefaultShippingMethod.vue";
import PaczkomatyShippingMethod from "@shopen/components/frontend/shipping/methods/PaczkomatyShippingMethod.vue";

const props = defineProps(['methods'])

</script>

<template>
    <div class="flex gap-2 flex-col">
        <template v-for="method in methods">
            <PaczkomatyShippingMethod v-if="method.key === 'paczkomaty'" :method="method"/>
            <DefaultShippingMethod v-else :method="method"/>
        </template>
    </div>
</template>