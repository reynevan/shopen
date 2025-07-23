<script setup>

import Cover from "@shopen/components/admin/Cover.vue";
import Sidebar from "@shopen/components/admin/layout/Sidebar.vue";


</script>

<template>
    <Sidebar/>
    <div class="pl-[100px]">
        <main>
            <slot/>
        </main>
    </div>
    <Teleport to="body">
        <Cover/>
    </Teleport>
</template>