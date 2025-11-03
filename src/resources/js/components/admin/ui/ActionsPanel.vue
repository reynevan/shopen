<script setup>
import {Link, router, usePage} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import {computed} from "vue";

const props = defineProps({
    backRoute: {type: String}
})

const page = usePage()

const goBack = () => {
    if (page.props.referer) {
        router.visit(page.props.referer)
    } else {
        router.visit(route(props.backRoute))
    }
}

const hasHistory = computed(() => (window.history.length > 1) || page.props.referer)

const backLinkClass = 'text-2xl text-gray-600 hover:text-black transition-colors duration-300 cursor-pointer'
</script>

<template>

    <div class="bg-accent px-4 py-4 mb-8 flex gap-6 items-center justify-between sticky top-0 z-100 h-[72px]">
        <div class="flex items-stretch h-full">
            <div v-if="backRoute" class="border-r pr-4 mr-4 h-full flex items-center">
                <button v-if="hasHistory" @click="goBack" :class="backLinkClass">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <Link v-else :href="route(backRoute)" :class="backLinkClass">
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

</template>