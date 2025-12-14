<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    routeName: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
});

const page = usePage();

const isActive = computed(() => {
    const currentSegments = page.props.route.split('.');
    const propsSegments = props.routeName.split('.');

    if (currentSegments.length < 2 || propsSegments.length < 2) {
        return false;
    }

    return currentSegments[0] === propsSegments[0] &&
        currentSegments[1] === propsSegments[1];
});

</script>

<template>
    <li class="rounded hover:bg-accent transition-all" :class="{ 'bg-accent': isActive }">
        <Link prefetch class="py-2 px-4 flex items-center gap-2 w-full h-full" :href="route(routeName)">
            <slot v-if="isActive" name="iconActive" />
            <slot v-else name="iconInactive" />
            <span>{{ label }}</span>
        </Link>
    </li>
</template>