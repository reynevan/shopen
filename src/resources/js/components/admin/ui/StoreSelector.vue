<script setup>
    import {usePage} from "@inertiajs/vue3";

    const page = usePage()
</script>

<template>
    stores: {{ page.props.stores }}
</template>