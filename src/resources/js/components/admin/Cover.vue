<script setup>
    import {useCoverStore} from "@shopen/stores/cover.js";

    const cover = useCoverStore();
</script>

<template>
    <div class="cover fixed top-0 left-0 bottom-0 right-0 z-20 bg-black/60 transition-opacity duration-500"
         :class="{'opacity-0 pointer-events-none': !cover.visible, 'opacity-100': cover.visible}"
         @click="cover.close()"></div>
</template>

<style scoped>

</style>