<script setup>

import AppLayout from "./AppLayout.vue";
import UserNav from "@shopen/components/frontend/user/nav/UserNav.vue";

</script>

<template>
    <AppLayout>
        <div class="main-container">
            <div class="flex flex-col sm:flex-row bg-body px-6 py-12">
                <UserNav/>
                <div class="flex-1 min-w-0 sm:border-l sm:border-light sm:pl-6 sm:ml-6">
                    <slot/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>