<script setup>
import SettingsLayout from "@shopen/layouts/admin/SettingsLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {router, useForm} from "@inertiajs/vue3";
import Button from "../../../components/admin/ui/Button.vue";
import ActionButton from "../../../components/admin/ui/ActionButton.vue";

defineOptions({layout: SettingsLayout})

const props = defineProps({
    connectUrl: {type: String}
})


</script>

<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Instagram</PageTitle>
        </template>
        <Button @click="save">Zapisz</Button>
    </ActionsPanel>
    <section>
        <a :href="connectUrl">Połącz</a>
    </section>
</template>