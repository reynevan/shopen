<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    links: {
        type: Array
    },
    only: {
        type: Array
    },
    preserveScroll: {
        type: Boolean,
        default: false
    },
    to: {
        type: String
    }
})

const emits = defineEmits(['onPaginate'])

</script>

<template>
    <nav v-if="links.length > 3" class="flex justify-center mt-8">
        <div class="flex divide-x divide-border-light">
            <template v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url + (to ?? '')"
                    prefetch
                    :class="[
                    'px-4 py-2 text-sm transition-colors',
                        link.active ? 'bg-accent/80'
                            : link.url ? 'hover:bg-accent'
                            : 'opacity-60 cursor-not-allowed'
                    ]"
                    :only="only"
                    :preserve-scroll="preserveScroll"
                    preserve-state
                    v-html="link.label"
                />
            </template>
        </div>
    </nav>
</template>