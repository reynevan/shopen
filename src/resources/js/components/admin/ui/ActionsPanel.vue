<script setup>
import {Link, router, usePage} from "@inertiajs/vue3";

const props = defineProps({
    backRoute: {type: String},
    backRouteParams: {type: Array, default: []}
})

const page = usePage()


const backLinkClass = 'text-2xl text-gray-600 hover:text-black transition-colors duration-300 cursor-pointer'
</script>

<template>
    <Teleport to="body">
        <div class="bg-accent px-4 py-4 mb-8 flex gap-6 items-center justify-between fixed top-0 left-[100px] right-0 z-100 h-[72px]">
            <div class="flex items-stretch h-full">
                <div v-if="backRoute" class="border-r pr-4 mr-4 h-full flex items-center">
                    <Link :href="route(backRoute, backRouteParams)" :class="backLinkClass">
                        <i class="bi bi-chevron-left"></i>
                    </Link>
                </div>
                <div class="flex items-center">
                    <slot name="title"/>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4">
                <slot name="default"/>
            </div>
        </div>
    </Teleport>
</template>