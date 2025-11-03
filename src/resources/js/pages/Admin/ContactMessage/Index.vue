<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import ContactMessagesTable from "@shopen/pages/Admin/ContactMessage/components/ContactMessagesTable.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    messages: {type: Object, required: true},
})
</script>
<template>
    <ActionsPanel>
        <template #title>Wiadomości</template>
    </ActionsPanel>
    <ContactMessagesTable :messages="messages"/>
</template>