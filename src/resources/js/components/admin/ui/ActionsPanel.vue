<script setup>
import {Link} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";

defineProps({
    backRoute: {type: String}
})
</script>

<template>

    <div class="bg-accent px-4 py-4 mb-8 flex gap-6 items-center justify-between">
        <Link v-if="backRoute" :href="route(backRoute)"><Button>Wstecz</Button></Link>
        <div class="w-full flex items-center justify-end gap-4">
            <slot name="default"/>
        </div>
    </div>

</template>