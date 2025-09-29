<script setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    brand: {type: Object}
})

const computedSrcset = computed(() => {
    if (!props.brand.logo || Object.keys(props.brand.logo).length === 0) {
        return '';
    }

    return Object.entries(props.brand.logo)
        .map(([key, url]) => {
            const width = key;
            return {width, url};
        })
        .sort((a, b) => a.width - b.width)
        .map(item => `${item.url} ${item.width}`)
        .join(', ');
});

const fallbackSrc = computed(() => {
    return Object.values(props.brand.logo)[0] || '';
});


</script>

<template>
    <div class="w-full px-2 flex justify-center">
        <div class="w-full flex justify-center items-center py-4 px-4 border border-light rounded group">
            <Link :href="brand.url" class="h-[80px] flex items-center">
                <img
                    class="max-h-full grayscale group-hover:grayscale-0 transition-all"
                    v-if="brand.logo && computedSrcset"
                    :src="fallbackSrc"
                    :srcset="computedSrcset"
                    :alt="brand.name"
                    :height="80"
                />
            </Link>
        </div>
    </div>
</template>