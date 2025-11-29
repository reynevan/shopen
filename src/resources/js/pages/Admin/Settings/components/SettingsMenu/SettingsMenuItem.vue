<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
const props = defineProps({
    title: {type: String},
    routeName: {type: String},
})

const page = usePage()
const isActive = computed(() => page.props.route === props.routeName)
</script>

<template>
    <li class="px-4 py-2" :class="[isActive ? 'bg-accent' : 'hover:bg-accent/20']">
        <Link :href="route(props.routeName)">
            {{ title }}
        </Link>
    </li>
</template>