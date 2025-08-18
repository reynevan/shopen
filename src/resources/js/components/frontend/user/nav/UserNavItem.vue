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
    return page.props.ziggy.name === props.routeName;
});

</script>

<template>
    <li class="rounded hover:bg-accent transition-all" :class="{ 'bg-accent': isActive }">
        <Link class="py-2 px-4 flex items-center gap-2 w-full h-full" :href="route(routeName)">
            <slot v-if="isActive" name="iconActive" />
            <slot v-else name="iconInactive" />
            <span>{{ label }}</span>
        </Link>
    </li>
</template>