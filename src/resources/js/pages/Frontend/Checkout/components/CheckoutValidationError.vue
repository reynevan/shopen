<script setup>

import {usePage} from "@inertiajs/vue3";
import IconWarning from "../../../../components/icons/IconWarning.vue";

const page = usePage();

const props = defineProps(['section'])
</script>

<template>
    <div class="error-msg w-full" v-if="page.props.errors[section]">
        <IconWarning size="lg"/>
        <div>{{ page.props.errors[section] }}</div>
    </div>
</template>

<style scoped>

</style>