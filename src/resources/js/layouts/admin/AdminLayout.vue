<script setup>

import Cover from "@shopen/components/admin/Cover.vue";
import Sidebar from "@shopen/components/admin/layout/Sidebar.vue";
import FlashMessage from "@shopen/components/admin/ui/FlashMessage.vue";

</script>

<template>
    <Sidebar/>
    <div class="pl-[100px]">
        <main class="pb-20 pt-[72px] px-6">
            <slot/>
        </main>
    </div>
    <FlashMessage/>
    <Teleport to="body">
        <Cover/>
    </Teleport>
</template>