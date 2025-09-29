<script setup>
import {Link} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";

defineProps({
    backRoute: {type: String}
})
</script>

<template>

    <div class="bg-accent px-4 py-4 mb-8 flex gap-6 items-center justify-between sticky top-0 z-100 h-[72px]">
        <div class="flex items-center">
            <Link v-if="backRoute" :href="route(backRoute)"><Button type="ghost">Wstecz</Button></Link>
            <slot name="title"/>
        </div>
        <div class="flex items-center justify-end gap-4">
            <slot name="default"/>
        </div>
    </div>

</template>