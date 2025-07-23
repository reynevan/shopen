<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    'links': {
        type: Array
    },
    'only': {
        type: Array
    }
})
</script>

<template>
    <nav v-if="links.length > 3" class="flex justify-center mt-8">
        <div class="flex space-x-1">
            <template v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    prefetch
                    :class="[
                    'px-4 py-2 text-sm rounded-md border transition-colors',
                        link.active ? 'bg-blue-600 text-white border-blue-600'
                            : link.url ? 'text-gray-700 border-gray-300 hover:bg-gray-50'
                            : 'text-gray-400 border-gray-200 cursor-not-allowed'
                    ]"
                    :only="only"
                    preserve-state
                    v-html="link.label"
                />
            </template>
        </div>
    </nav>
</template>