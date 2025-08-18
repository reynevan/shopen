<script setup>

import {useCoverStore} from "@shopen/stores/cover.js";
import IconMenu from "@shopen/components/icons/IconMenu.vue";

const cover = useCoverStore();

cover.onClose(() => {
    document.body.classList.remove('nav-open')
})

const openMenu = () =>  {
    cover.open();
    document.body.classList.add('nav-open')
}

</script>

<template>
    <IconMenu @click="openMenu"></IconMenu>
</template>