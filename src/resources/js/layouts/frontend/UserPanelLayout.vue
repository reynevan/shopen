<script setup>

import AppLayout from "./AppLayout.vue";
import UserNav from "../../components/frontend/user/nav/UserNav.vue";
</script>

<template>
<AppLayout>
    <div class="flex flex-col sm:flex-row">
        <UserNav/>
        <div class="flex-1 min-w-0 sm:border-l sm:border-light sm:pl-6 sm:ml-6">
            <slot/>
        </div>
    </div>
</AppLayout>
</template>