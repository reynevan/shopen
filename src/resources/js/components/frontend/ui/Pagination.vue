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
                <div v-if="link.url">
                    <Link
                        :href="link.url + (to ?? '')"
                        prefetch
                        :class="[
                            'pagination-item',
                            link.previous ? 'pagination-item-prev' : '',
                            link.next ? 'pagination-item-next' : '',
                            link.active ? 'pagination-item-active'
                                : link.url ? 'pagination-item-url' : 'pagination-item-no-url'
                        ]"
                        :only="only"
                        :preserve-scroll="preserveScroll"
                        preserve-state
                        :title="link.previous ? 'Poprzednia strona' : link.next ? 'Następna strona' : 'Strona ' + link.label"
                    >
                    <span v-if="link.previous">
                        <IconChevron left size="2xl"/>
                    </span>
                        <span v-else-if="link.next" class="pagination-item-next">
                        <IconChevron right size="2xl"/>
                    </span>
                        <span v-else>
                        {{ link.label }}
                    </span>
                    </Link>
                </div>
            </template>
        </div>
    </nav>
</template>