<script setup>

import Cover from "@shopen/components/admin/Cover.vue";
import Sidebar from "@shopen/components/admin/layout/Sidebar.vue";
import FlashMessage from "@shopen/components/admin/ui/FlashMessage.vue";
import SettingsMenu from "../../pages/Admin/Settings/components/SettingsMenu/SettingsMenu.vue";

</script>

<template>
    <Sidebar/>
    <div class="pl-[100px]">
        <main class="pb-20 pt-[102px] px-6">
            <div class="flex gap-2">
                <div class="border-r pr-4 mr-4">
                    <SettingsMenu/>
                </div>
                <slot/>
            </div>
        </main>
    </div>
    <FlashMessage/>
    <Teleport to="body">
        <Cover/>
    </Teleport>
</template>