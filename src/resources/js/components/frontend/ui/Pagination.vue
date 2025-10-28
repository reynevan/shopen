<script setup>

import {Link, router} from "@inertiajs/vue3";
import IconChevron from "../../icons/IconChevron.vue";
import {ref} from "vue";

const props = defineProps({
    meta: {
        type: Object
    },
    links: {
        type: Object
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

const pageInput = ref(null)

const goToPage = () => {
    const pageNumber = parseInt(pageInput.value)

    if (pageNumber && pageNumber >= 1 && pageNumber <= props.meta.last_page) {
        const url = new URL(window.location.href)
        url.searchParams.set('strona', pageNumber)

        router.visit(url.toString(), {
            only: props.only,
            preserveScroll: props.preserveScroll,
            preserveState: true
        })
    }
}

</script>

<template>
    <nav v-if="meta.links?.length > 3" class="flex justify-center mt-8">
        <div class="pagination sm:flex hidden">
            <template v-for="(link, index) in meta?.links" :key="index">
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
        <div class="pagination flex gap-4 items-center sm:hidden">
            <Link class="pagination-button" v-if="links.prev" :href="links.prev" title="Poprzednia strona">
                <div class="w-4 h-4 border-neutral-600 border-b border-l transform rotate-45"></div>
            </Link>
            <div class="flex items-center gap-2">
                <input
                    id="page-number"
                    class="pagination-page-input"
                    type="number"
                    v-model="pageInput"
                    @keyup.enter="goToPage"
                    :min="1"
                    :max="meta.last_page"
                    :placeholder="meta.current_page"
                >
                <span class="whitespace-nowrap pagination-item"> z {{ meta.last_page }}</span>
            </div>
            <Link class="pagination-button" v-if="links.next" :href="links.next" title="Następna strona">
                <div class="w-4 h-4 border-neutral-600 border-t border-r transform rotate-45"></div>
            </Link>
        </div>
    </nav>
</template>