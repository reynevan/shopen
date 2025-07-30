<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Breadcrumbs from "@shopen/components/frontend/Breadcrumbs.vue";


</script>

<template>
    <Header/>
    <div class="container mx-auto max-w-7xl px-4">
        <Breadcrumbs/>
        <FlashMessage/>
        <slot/>
    </div>
    <Teleport to="body">
        <Cover/>
    </Teleport>
</template>