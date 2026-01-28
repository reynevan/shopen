<script setup>
import SettingsLayout from "@shopen/layouts/admin/SettingsLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {router} from "@inertiajs/vue3";
import Button from "../../../components/admin/ui/Button.vue";
import FormField from "../../../components/admin/form/FormField.vue";

defineOptions({layout: SettingsLayout})

const props = defineProps({
    connectUrl: {type: String},
    isConnected: {type: Boolean}
})

const disconnect = () => {
    router.post(route('admin.settings.instagram.disconnect'));
}

</script>

<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Instagram</PageTitle>
        </template>
    </ActionsPanel>
    <section>
        <div>
            <FormField label="Status">
                <div class="pt-2 h-full">
                    <div v-if="isConnected" class="flex gap-2 items-center h-full">
                        <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                        <div class="text-xs uppercase">Połączony</div>
                    </div>
                    <div v-else class="flex gap-2 items-center h-full">
                        <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                        <div class="text-xs uppercase">Nie połączony</div>
                    </div>
                </div>
            </FormField>
        </div>
        <a v-if="!isConnected" :href="connectUrl">
            <Button type="primary">Połącz</Button>
        </a>
        <Button v-else type="primary" @click="disconnect">Rozłącz</Button>
    </section>
</template>