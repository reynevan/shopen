<script setup>
import {Link} from "@inertiajs/vue3";

    defineProps({
        meta: {type: Object},
        only: {type: Array, default: []},
        preserveScroll: {type: Boolean, default: false},
    })
</script>

<template>
    <nav v-if="meta.links.length > 3" class="flex justify-center mt-8">
        <div class="flex divide-x divide-light">
            <template v-for="(link, index) in meta.links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    prefetch
                    :class="[
                        'px-4 py-2 text-sm transition-colors',
                        link.active ? 'bg-light'
                        : link.url ? 'text-gray-700 border-gray-300 hover:bg-accent/50'
                        : 'text-gray-400 cursor-not-allowed'
                    ]"
                    :preserve-scroll="preserveScroll"
                    :only="only"
                    preserve-state
                    v-html="link.label"
                />
            </template>
        </div>
    </nav>
</template>