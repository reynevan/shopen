<script setup>

import {usePage} from "@inertiajs/vue3";
import SocialButton from "./SocialButton.vue";

const page = usePage()

</script>

<template>
    <div v-if="page.props.providers && page.props.providers.length">
        <div class="flex flex-col gap-4">
            <SocialButton v-for="provider in page.props.providers" :provider="provider"/>
        </div>
        <div class="my-6 flex items-center gap-4 text-neutral-500 text-sm">
            <div class="w-full h-[1px] border-b border-light"></div>
            <div class="whitespace-nowrap">
                lub
            </div>
            <div class="w-full h-[1px] border-b border-light"></div>
        </div>
    </div>
</template>