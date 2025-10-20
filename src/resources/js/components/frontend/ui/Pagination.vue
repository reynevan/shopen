<script setup>

import {Link} from "@inertiajs/vue3";
import IconChevron from "../../icons/IconChevron.vue";

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
        <div class="pagination flex">
            <template v-for="(link, index) in links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url + (to ?? '')"
                    prefetch
                    :class="[
                        'pagination-item',
                        link.active ? 'pagination-item-active'
                            : link.url ? 'pagination-item-url' : 'pagination-item-no-url'
                    ]"
                    :only="only"
                    :preserve-scroll="preserveScroll"
                    preserve-state
                >
                    <span v-if="link.previous">
                        <IconChevron left size="2xl"/>
                    </span>
                    <span v-else-if="link.next">
                        <IconChevron right size="2xl"/>
                    </span>
                    <span v-else>
                        {{ link.label }}
                    </span>
                </Link>
            </template>
        </div>
    </nav>
</template>