<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "../../components/frontend/layout/header/Header.vue";
import FlashMessage from "../../components/frontend/ui/FlashMessage.vue";


</script>

<template>
    <Header/>
    <div class="container mx-auto max-w-7xl px-4">
        <FlashMessage/>
        <slot/>
    </div>
    <Teleport to="body">
        <Cover/>
    </Teleport>
</template>