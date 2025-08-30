<script setup>

import {defineAsyncComponent} from "vue";
import DefaultShippingMethod from "@shopen/components/frontend/shipping/methods/DefaultShippingMethod.vue";

const props = defineProps(['methods'])

const projectComponents = import.meta.glob('/resources/js/components/frontend/shipping/methods/**/*.vue')
const coreComponents = import.meta.glob('/vendor/shopen/core/src/resources/js/components/frontend/shipping/methods/**/*.vue')

function resolveComponent(path) {
    const projectPath = `/resources/js/components/frontend/shipping/methods/${path}.vue`
    const corePath = `/vendor/shopen/core/src/resources/js/components/frontend/shipping/methods/${path}.vue`

    if (projectComponents[projectPath]) {
        return defineAsyncComponent(projectComponents[projectPath])
    }

    if (coreComponents[corePath]) {
        return defineAsyncComponent(coreComponents[corePath])
    }

    return defineAsyncComponent(() =>
        import('/vendor/shopen/core/src/resources/js/components/frontend/shipping/methods/DefaultShippingMethod.vue')
    )
}

</script>

<template>
    <div class="flex gap-2 flex-col">
        <template v-for="method in methods">
            <component v-if="method.component" :is="resolveComponent(method.component)" :method="method"/>
            <DefaultShippingMethod v-else :method="method"/>
        </template>
    </div>
</template>